<?php

namespace App\Services;

use App\Models\Violation;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ViolationNotificationService
{
    protected MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Send notification to the student (accused/complained against)
     */
    public function notifyStudent(Violation $violation): bool
    {
        $student = $violation->student;
        $studentProfile = $student->studentProfile;

        if (!$student || !$student->email) {
            Log::warning("Cannot notify student - no email found for violation ID: {$violation->id}");
            return false;
        }

        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : $student->name;

        $subject = "FEATI University - Violation Report Notice";
        
        $htmlBody = $this->getStudentNotificationTemplate($violation, $studentName, $studentProfile);

        return $this->mailService->send(
            $student->email,
            $studentName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Send notification to the complainant (reporter)
     */
    public function notifyComplainant(Violation $violation): bool
    {
        $reporter = $violation->reporter;
        
        if (!$reporter || !$reporter->email) {
            Log::warning("Cannot notify complainant - no email found for violation ID: {$violation->id}");
            return false;
        }

        $reporterProfile = $reporter->profile ?? $reporter->studentProfile;
        $reporterName = $reporterProfile 
            ? trim($reporterProfile->first_name . ' ' . $reporterProfile->last_name)
            : $reporter->name;

        $subject = "FEATI University - Complaint Submission Confirmation";
        
        $htmlBody = $this->getComplainantNotificationTemplate($violation, $reporterName);

        return $this->mailService->send(
            $reporter->email,
            $reporterName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Send notification to OSA about new violation/complaint
     */
    public function notifyOSA(Violation $violation): bool
    {
        // Get all OSA users
        $osaUsers = User::whereHas('role', function($q) {
            $q->where('name', 'osa');
        })->get();

        $success = true;
        foreach ($osaUsers as $osaUser) {
            $osaProfile = $osaUser->profile;
            $osaName = $osaProfile 
                ? trim($osaProfile->first_name . ' ' . $osaProfile->last_name)
                : $osaUser->name;

            $subject = "FEATI University - New Violation Report Submitted";
            $htmlBody = $this->getOSANotificationTemplate($violation, $osaName);

            if (!$this->mailService->send($osaUser->email, $osaName, $subject, $htmlBody)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send notification when violation status is updated
     */
    public function notifyStatusUpdate(Violation $violation, string $oldStatus): bool
    {
        $student = $violation->student;
        $studentProfile = $student->studentProfile;

        if (!$student || !$student->email) {
            return false;
        }

        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : $student->name;

        $subject = "FEATI University - Violation Status Update";
        $htmlBody = $this->getStatusUpdateTemplate($violation, $studentName, $oldStatus);

        return $this->mailService->send(
            $student->email,
            $studentName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Send notification when verdict/sanction is given
     */
    public function notifyVerdict(Violation $violation): bool
    {
        $student = $violation->student;
        $studentProfile = $student->studentProfile;

        if (!$student || !$student->email) {
            return false;
        }

        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : $student->name;

        $subject = "FEATI University - Violation Verdict Decision";
        $htmlBody = $this->getVerdictNotificationTemplate($violation, $studentName, $studentProfile);

        // Also notify the complainant if it was a student complaint
        $reporter = $violation->reporter;
        if ($reporter && $reporter->role?->name === 'student') {
            $this->notifyComplainantVerdict($violation);
        }

        return $this->mailService->send(
            $student->email,
            $studentName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Notify complainant about the verdict
     */
    protected function notifyComplainantVerdict(Violation $violation): bool
    {
        $reporter = $violation->reporter;
        $reporterProfile = $reporter->profile ?? $reporter->studentProfile;

        if (!$reporter || !$reporter->email) {
            return false;
        }

        $reporterName = $reporterProfile 
            ? trim($reporterProfile->first_name . ' ' . $reporterProfile->last_name)
            : $reporter->name;

        $subject = "FEATI University - Your Complaint Has Been Resolved";
        $htmlBody = $this->getComplainantVerdictTemplate($violation, $reporterName);

        return $this->mailService->send(
            $reporter->email,
            $reporterName,
            $subject,
            $htmlBody
        );
    }

    /**
     * Send all notifications for a new violation
     */
    public function sendNewViolationNotifications(Violation $violation): array
    {
        $results = [
            'student' => $this->notifyStudent($violation),
            'complainant' => $this->notifyComplainant($violation),
            'osa' => $this->notifyOSA($violation),
        ];

        Log::info("Violation notifications sent for ID: {$violation->id}", $results);

        return $results;
    }

    // ============ EMAIL TEMPLATES ============

    protected function getStudentNotificationTemplate(Violation $violation, string $studentName, $studentProfile): string
    {
        $studentNumber = $studentProfile->student_number ?? 'N/A';
        $program = $studentProfile->program ?? 'N/A';
        $violationDate = $violation->violation_date?->format('F d, Y') ?? 'N/A';
        $reporterProfile = $violation->reporter?->profile ?? $violation->reporter?->studentProfile;
        $reporterName = $reporterProfile 
            ? trim($reporterProfile->first_name . ' ' . $reporterProfile->last_name)
            : ($violation->reporter?->name ?? 'Anonymous');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Violation Report Notice</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1e40af; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .warning { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FEATI University</h1>
            <h2>Office of Student Affairs</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$studentName}</strong>,</p>
            
            <p>This is to inform you that a violation report has been filed against you. Please review the details below:</p>
            
            <div class="info-box">
                <p><span class="label">Student Number:</span> {$studentNumber}</p>
                <p><span class="label">Program:</span> {$program}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Offense Category:</span> {$violation->offense_category}</p>
                <p><span class="label">Date of Incident:</span> {$violationDate}</p>
                <p><span class="label">Reported By:</span> {$reporterName}</p>
                <p><span class="label">Description:</span><br>{$violation->description}</p>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> You are advised to visit the Office of Student Affairs (OSA) within 3 working days to address this matter. Failure to respond may result in default judgment.
            </div>
            
            <p>You have the right to:</p>
            <ul>
                <li>Present your side of the story</li>
                <li>Submit evidence in your defense</li>
                <li>File an appeal if necessary</li>
            </ul>
            
            <p>If you have any questions, please contact the Office of Student Affairs.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getComplainantNotificationTemplate(Violation $violation, string $reporterName): string
    {
        $studentProfile = $violation->student?->studentProfile;
        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : ($violation->student?->name ?? 'Unknown');
        $violationDate = $violation->violation_date?->format('F d, Y') ?? 'N/A';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complaint Submission Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1e40af; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .success { background-color: #d1fae5; border-left: 4px solid #10b981; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FEATI University</h1>
            <h2>Office of Student Affairs</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$reporterName}</strong>,</p>
            
            <div class="success">
                <strong>✓ Complaint Received</strong><br>
                Your complaint has been successfully submitted and is now under review.
            </div>
            
            <p>Here are the details of your submitted complaint:</p>
            
            <div class="info-box">
                <p><span class="label">Reference Number:</span> VIO-{$violation->id}</p>
                <p><span class="label">Complaint Against:</span> {$studentName}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Offense Category:</span> {$violation->offense_category}</p>
                <p><span class="label">Date of Incident:</span> {$violationDate}</p>
                <p><span class="label">Status:</span> {$violation->status}</p>
            </div>
            
            <p><strong>What happens next?</strong></p>
            <ul>
                <li>Your complaint will be reviewed by the Office of Student Affairs</li>
                <li>The accused student will be notified and asked to respond</li>
                <li>You may be contacted for additional information if needed</li>
                <li>You will receive an update once a decision has been made</li>
            </ul>
            
            <p>Thank you for helping maintain the integrity and discipline of our university community.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getOSANotificationTemplate(Violation $violation, string $osaName): string
    {
        $studentProfile = $violation->student?->studentProfile;
        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : ($violation->student?->name ?? 'Unknown');
        $studentNumber = $studentProfile->student_number ?? 'N/A';
        $program = $studentProfile->program ?? 'N/A';
        
        $reporterProfile = $violation->reporter?->profile ?? $violation->reporter?->studentProfile;
        $reporterName = $reporterProfile 
            ? trim($reporterProfile->first_name . ' ' . $reporterProfile->last_name)
            : ($violation->reporter?->name ?? 'Unknown');
        $reporterRole = $violation->reporter?->role?->label ?? 'Unknown';
        
        $violationDate = $violation->violation_date?->format('F d, Y') ?? 'N/A';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Violation Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .alert { background-color: #fee2e2; border-left: 4px solid #dc2626; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
        .btn { display: inline-block; background-color: #1e40af; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ New Violation Report</h1>
            <h2>Requires Your Attention</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$osaName}</strong>,</p>
            
            <div class="alert">
                <strong>Action Required:</strong> A new violation report has been submitted and requires review.
            </div>
            
            <h3>Student Information</h3>
            <div class="info-box">
                <p><span class="label">Name:</span> {$studentName}</p>
                <p><span class="label">Student Number:</span> {$studentNumber}</p>
                <p><span class="label">Program:</span> {$program}</p>
            </div>
            
            <h3>Violation Details</h3>
            <div class="info-box">
                <p><span class="label">Reference Number:</span> VIO-{$violation->id}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Offense Category:</span> {$violation->offense_category}</p>
                <p><span class="label">Date of Incident:</span> {$violationDate}</p>
                <p><span class="label">Description:</span><br>{$violation->description}</p>
            </div>
            
            <h3>Reporter Information</h3>
            <div class="info-box">
                <p><span class="label">Reported By:</span> {$reporterName}</p>
                <p><span class="label">Role:</span> {$reporterRole}</p>
            </div>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{app_url}}/violations/{$violation->id}/edit" class="btn">Review Violation</a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getStatusUpdateTemplate(Violation $violation, string $studentName, string $oldStatus): string
    {
        $newStatus = ucfirst(str_replace('_', ' ', $violation->status));
        $oldStatusFormatted = ucfirst(str_replace('_', ' ', $oldStatus));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Violation Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1e40af; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .status-change { background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FEATI University</h1>
            <h2>Violation Status Update</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$studentName}</strong>,</p>
            
            <p>This is to inform you that the status of your violation case has been updated.</p>
            
            <div class="status-change">
                <p><strong>Status Changed:</strong></p>
                <p>{$oldStatusFormatted} → <strong>{$newStatus}</strong></p>
            </div>
            
            <div class="info-box">
                <p><span class="label">Reference Number:</span> VIO-{$violation->id}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Current Status:</span> {$newStatus}</p>
            </div>
            
            <p>If you have any questions or concerns, please visit the Office of Student Affairs.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getVerdictNotificationTemplate(Violation $violation, string $studentName, $studentProfile): string
    {
        $studentNumber = $studentProfile->student_number ?? 'N/A';
        $sanction = $violation->sanction ?? 'To be determined';
        $actionTaken = $violation->action_taken ?? 'N/A';
        $resolutionDate = $violation->resolution_date?->format('F d, Y') ?? now()->format('F d, Y');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Violation Verdict Decision</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #7c3aed; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .verdict { background-color: #fef3c7; border: 2px solid #f59e0b; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FEATI University</h1>
            <h2>Office of Student Affairs - Verdict</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$studentName}</strong>,</p>
            <p><strong>Student Number:</strong> {$studentNumber}</p>
            
            <p>After careful review and deliberation, the Office of Student Affairs has reached a decision regarding your violation case.</p>
            
            <div class="info-box">
                <p><span class="label">Reference Number:</span> VIO-{$violation->id}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Offense Category:</span> {$violation->offense_category}</p>
            </div>
            
            <div class="verdict">
                <h3 style="margin-top: 0;">📋 VERDICT</h3>
                <p><span class="label">Sanction Imposed:</span> <strong>{$sanction}</strong></p>
                <p><span class="label">Action Taken:</span> {$actionTaken}</p>
                <p><span class="label">Resolution Date:</span> {$resolutionDate}</p>
            </div>
            
            <p><strong>Your Rights:</strong></p>
            <ul>
                <li>You may file an appeal within 5 working days if you disagree with this decision</li>
                <li>Visit the Office of Student Affairs to discuss the terms of your sanction</li>
                <li>Failure to comply with the imposed sanction may result in additional penalties</li>
            </ul>
            
            <p>If you have any questions, please contact the Office of Student Affairs immediately.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    protected function getComplainantVerdictTemplate(Violation $violation, string $reporterName): string
    {
        $studentProfile = $violation->student?->studentProfile;
        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : ($violation->student?->name ?? 'Unknown');
        $sanction = $violation->sanction ?? 'To be determined';
        $status = ucfirst(str_replace('_', ' ', $violation->status));

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complaint Resolution</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #059669; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .info-box { background-color: #fff; border: 1px solid #e5e7eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .label { font-weight: bold; color: #4b5563; }
        .resolved { background-color: #d1fae5; border-left: 4px solid #10b981; padding: 10px; margin: 15px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FEATI University</h1>
            <h2>Complaint Resolution Notice</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{$reporterName}</strong>,</p>
            
            <div class="resolved">
                <strong>✓ Your complaint has been resolved</strong>
            </div>
            
            <p>We would like to inform you that your complaint has been reviewed and a decision has been made.</p>
            
            <div class="info-box">
                <p><span class="label">Reference Number:</span> VIO-{$violation->id}</p>
                <p><span class="label">Complaint Against:</span> {$studentName}</p>
                <p><span class="label">Violation Type:</span> {$violation->violation_type}</p>
                <p><span class="label">Final Status:</span> {$status}</p>
                <p><span class="label">Sanction Imposed:</span> {$sanction}</p>
            </div>
            
            <p>Thank you for bringing this matter to our attention. Your participation helps maintain the standards and integrity of our university community.</p>
            
            <p>If you have any further concerns, please don't hesitate to contact the Office of Student Affairs.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from FEATI University Student Information System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
