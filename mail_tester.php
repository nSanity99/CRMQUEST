<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/mailer/mailer_config.php';

$mail = new PHPMailer(true);

// DEBUG: SCRIVI QUI SEMPRE
$mail->SMTPDebug = 2;
$mail->Debugoutput = function ($str, $level) {
    error_log("[PHPMailer][$level] $str");
};

try {
    $mail->isSMTP();
    $mail->Host       = $mail_host;
    $mail->Port       = $mail_port;
    $mail->SMTPSecure = $mail_secure;
    $mail->SMTPAuth   = true;
    $mail->Username   = $mail_username;
    $mail->Password   = $mail_password;

    $mail->setFrom($mail_from, $mail_from_name);
    $mail->addAddress($mail_to);

    $mail->isHTML(true);
    $mail->Subject = 'TEST invio email PHPMailer';
    $mail->Body    = 'Questa è una mail di test inviata da PHPMailer.';

    $mail->send();
    echo "✅ Email inviata con successo.";
} catch (Exception $e) {
    echo "❌ Errore durante l'invio: " . $mail->ErrorInfo;
    error_log("[mail_tester] Exception: " . $e->getMessage());
    error_log("[mail_tester] ErrorInfo: " . $mail->ErrorInfo);
}
