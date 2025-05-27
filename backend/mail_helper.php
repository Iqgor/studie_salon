<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//require_once __DIR__ . '/../vendor/autoload.php'; // Load PHPMailer

function sendMail(string $toEmail, string $subject, string $htmlBody, string $altBody): bool {
    $mail_host = getenv('MAIL_HOST');
    $mail_username = getenv('MAIL_USERNAME');
    $mail_password = getenv('MAIL_PASSWORD');

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $mail_host;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_username;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($mail_username, 'StudieSalon');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $altBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
