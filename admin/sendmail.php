<?php

require "../class/vendor/autoload.php";

$robo = 'singhtrisha@gmail.com';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$developmentMode = true;
$mailer = new PHPMailer($developmentMode);

try {
    $mailer->SMTPDebug = 2;
    $mailer->isSMTP();

    if ($developmentMode) {
    $mailer->SMTPOptions = [
        'ssl'=> [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        ]
    ];
    }

    $mailer->Mailer = "smtp";
    $mailer->Host="smtp.gmail.com";
   
    $mailer->SMTPAuth = true;
    $mailer->Username = 'singhtrisha239@gmail.com';
    $mailer->Password = 'Onu12345';
    $mailer->SMTPSecure = 'tls';
    $mailer->Port = 587;

    $mailer->setFrom('singhtrisha239@gmail.com', 'Kanchan');
    $mailer->addAddress('cutetrisha129@gmail.com', 'Kanchan');

    $mailer->isHTML(true);
    $mailer->Subject = 'PHPMailer Test';
    $mailer->Body = 'This is a <b>SAMPLE<b> email sent through <b>PHPMailer<b>';

    $mailer->send();
    $mailer->ClearAllRecipients();
    echo "MAIL HAS BEEN SENT SUCCESSFULLY";

} catch (Exception $e) {
    echo "EMAIL SENDING FAILED. INFO: " . $mailer->ErrorInfo;
}