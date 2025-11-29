<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    protected PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer with SMTP settings
     */
    protected function configure(): void
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host       = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = config('mail.mailers.smtp.username');
            $this->mailer->Password   = config('mail.mailers.smtp.password');
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = config('mail.mailers.smtp.port', 587);

            // Default sender
            $this->mailer->setFrom(
                config('mail.from.address', 'noreply@feati.edu'),
                config('mail.from.name', 'FEATI University - Student Affairs')
            );

            // Content settings
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';

        } catch (Exception $e) {
            Log::error('PHPMailer configuration error: ' . $e->getMessage());
        }
    }

    /**
     * Send an email
     */
    public function send(string $to, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        try {
            // Reset recipients for new email
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to, $toName);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);

            $this->mailer->send();
            
            Log::info("Email sent successfully to: {$to}");
            return true;

        } catch (Exception $e) {
            Log::error("Email sending failed: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Send email with CC
     */
    public function sendWithCC(string $to, string $toName, array $ccList, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearCCs();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to, $toName);
            
            foreach ($ccList as $cc) {
                $this->mailer->addCC($cc['email'], $cc['name'] ?? '');
            }

            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = $textBody ?: strip_tags($htmlBody);

            $this->mailer->send();
            
            Log::info("Email with CC sent successfully to: {$to}");
            return true;

        } catch (Exception $e) {
            Log::error("Email with CC sending failed: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Add attachment to the email
     */
    public function addAttachment(string $path, string $name = ''): self
    {
        try {
            $this->mailer->addAttachment($path, $name);
        } catch (Exception $e) {
            Log::error("Failed to add attachment: {$e->getMessage()}");
        }
        
        return $this;
    }

    /**
     * Set reply-to address
     */
    public function setReplyTo(string $email, string $name = ''): self
    {
        try {
            $this->mailer->addReplyTo($email, $name);
        } catch (Exception $e) {
            Log::error("Failed to set reply-to: {$e->getMessage()}");
        }
        
        return $this;
    }
}
