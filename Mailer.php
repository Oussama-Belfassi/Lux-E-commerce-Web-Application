<?php

namespace app;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host          = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth      = true;
        $this->mail->Username      = $_ENV['MAIL_USERNAME'];
        $this->mail->Password      = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure    = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port          = (int) $_ENV['MAIL_PORT'];
        $this->mail->Timeout       = 5;
        $this->mail->SMTPKeepAlive = false;

        $this->mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
        $this->mail->isHTML(true);
    }

    public function sendPasswordReset(string $toEmail, string $toName, string $resetLink): void
    {
        $this->mail->clearAddresses();
        $this->mail->addAddress($toEmail, $toName);
        $this->mail->Subject = 'Reset your password';
        $this->mail->Body    = "
            <p>Hi {$toName},</p>
            <p>You requested a password reset. Click the link below to set a new password.
               The link expires in <strong>1 hour</strong>.</p>
            <p><a href='{$resetLink}'>{$resetLink}</a></p>
            <p>If you did not request this, you can safely ignore this email.</p>
        ";
        $this->mail->AltBody = "Reset your password: {$resetLink} (expires in 1 hour)";

        $this->mail->send();
    }

    public static function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host          = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth      = true;
            $mail->Username      = $_ENV['MAIL_USERNAME'];
            $mail->Password      = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure    = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port          = (int) $_ENV['MAIL_PORT'];
            $mail->Timeout       = 5;
            $mail->SMTPKeepAlive = false;

            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            error_log('Email sent successfully to: ' . $to);
            return true;
        } catch (Exception $e) {
            error_log('Mailer error sending to ' . $to . ': ' . $e->getMessage());
            return false;
        }
    }
}
