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
        $this->mail->Host       = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAIL_USERNAME'];
        $this->mail->Password   = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = (int) $_ENV['MAIL_PORT'];
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
            <p>Click the link below to reset your password. Expires in <strong>1 hour</strong>.</p>
            <p><a href='{$resetLink}'>{$resetLink}</a></p>
            <p>If you did not request this, ignore this email.</p>
        ";
        $this->mail->AltBody = "Reset your password: {$resetLink}";
        $this->mail->send();
    }

    public static function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) $_ENV['MAIL_PORT'];
            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }
}

// <?php

// namespace app;

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// class Mailer
// {
//     public static function send(string $to, string $subject, string $body): bool
//     {
//         $apiKey = $_ENV['RESEND_API_KEY'];

//         $ch = curl_init('https://api.resend.com/emails');
//         curl_setopt_array($ch, [
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_POST           => true,
//             CURLOPT_HTTPHEADER     => [
//                 'Authorization: Bearer ' . $apiKey,
//                 'Content-Type: application/json',
//             ],
//             CURLOPT_POSTFIELDS => json_encode([
//                 'from'    => $_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM'] . '>',
//                 'to'      => [$to],
//                 'subject' => $subject,
//                 'html'    => $body,
//             ]),
//         ]);

//         $response = curl_exec($ch);
//         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         if ($httpCode === 200 || $httpCode === 201) {
//             error_log('Email sent successfully to: ' . $to);
//             return true;
//         }

//         error_log('Mailer error sending to ' . $to . ': ' . $response);
//         return false;
//     }

//     public function sendPasswordReset(string $toEmail, string $toName, string $resetLink): void
//     {
//         $sent = self::send(
//             $toEmail,
//             'Reset your password',
//             "
//             <p>Hi {$toName},</p>
//             <p>You requested a password reset. Click the link below to set a new password.
//                The link expires in <strong>1 hour</strong>.</p>
//             <p><a href='{$resetLink}'>{$resetLink}</a></p>
//             <p>If you did not request this, you can safely ignore this email.</p>
//             "
//         );

//         if (!$sent) {
//             throw new \RuntimeException('Failed to send password reset email.');
//         }
//     }
// }