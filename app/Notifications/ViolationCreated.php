<?php

namespace App\Notifications;

use App\Models\Violation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ViolationCreated extends Notification
{
    use Queueable;

    protected Violation $violation;
    protected string $recipientType; // 'student', 'complainant', 'osa'

    /**
     * Create a new notification instance.
     */
    public function __construct(Violation $violation, string $recipientType = 'student')
    {
        $this->violation = $violation;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $studentProfile = $this->violation->student?->studentProfile;
        $studentName = $studentProfile 
            ? trim($studentProfile->first_name . ' ' . $studentProfile->last_name)
            : ($this->violation->student?->name ?? 'Unknown');

        switch ($this->recipientType) {
            case 'complainant':
                return $this->getComplainantMail($studentName);
            case 'osa':
                return $this->getOSAMail($studentName);
            default:
                return $this->getStudentMail();
        }
    }

    protected function getStudentMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('FEATI University - Violation Report Notice')
            ->greeting('Dear Student,')
            ->line('This is to inform you that a violation report has been filed against you.')
            ->line('**Violation Type:** ' . $this->violation->violation_type)
            ->line('**Offense Category:** ' . $this->violation->offense_category)
            ->line('**Date of Incident:** ' . $this->violation->violation_date?->format('F d, Y'))
            ->line('**Description:** ' . $this->violation->description)
            ->action('View Details', url('/student/violations'))
            ->line('Please visit the Office of Student Affairs within 3 working days to address this matter.')
            ->salutation('Office of Student Affairs, FEATI University');
    }

    protected function getComplainantMail(string $studentName): MailMessage
    {
        return (new MailMessage)
            ->subject('FEATI University - Complaint Submission Confirmation')
            ->greeting('Dear Complainant,')
            ->line('Your complaint has been successfully submitted and is now under review.')
            ->line('**Reference Number:** VIO-' . $this->violation->id)
            ->line('**Complaint Against:** ' . $studentName)
            ->line('**Violation Type:** ' . $this->violation->violation_type)
            ->line('**Status:** ' . ucfirst($this->violation->status))
            ->action('View Status', url('/violations'))
            ->line('You will be notified once a decision has been made.')
            ->salutation('Office of Student Affairs, FEATI University');
    }

    protected function getOSAMail(string $studentName): MailMessage
    {
        $studentProfile = $this->violation->student?->studentProfile;
        
        return (new MailMessage)
            ->subject('FEATI University - New Violation Report Submitted')
            ->greeting('Dear OSA Staff,')
            ->line('A new violation report has been submitted and requires your review.')
            ->line('**Student:** ' . $studentName)
            ->line('**Student Number:** ' . ($studentProfile->student_number ?? 'N/A'))
            ->line('**Program:** ' . ($studentProfile->program ?? 'N/A'))
            ->line('**Violation Type:** ' . $this->violation->violation_type)
            ->line('**Offense Category:** ' . $this->violation->offense_category)
            ->action('Review Violation', url('/violations/' . $this->violation->id . '/edit'))
            ->line('Please review and take appropriate action.')
            ->salutation('FEATI University Student Information System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'violation_id' => $this->violation->id,
            'violation_type' => $this->violation->violation_type,
            'offense_category' => $this->violation->offense_category,
            'status' => $this->violation->status,
            'recipient_type' => $this->recipientType,
            'message' => $this->recipientType === 'student' 
                ? 'A violation has been filed against you'
                : ($this->recipientType === 'complainant' 
                    ? 'Your complaint has been submitted successfully'
                    : 'A new violation report requires your attention'),
        ];
    }
}
