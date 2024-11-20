<?php

namespace App\function;

use App\Class\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Permets d'envoyer un mail de confirmation d'inscription à l'utilisateur $user
 * @param \App\Class\User $user
 * @param int $code
 * @return bool
 */
function sendConfirmationCode(User $user, int $code)
{
    // code pour envoyer un mail à l'utilisateur $user
    try {

        //initialisation de l'objet PHPMailer
        $mailer = new PHPMailer(true);

        //configuration du SMTP
        $mailer->isSMTP();
        $mailer->Host       = 'smtp.gmail.com';
        $mailer->SMTPAuth   = true;
        $mailer->Username   = 'faouzanekouko@gmail.com';
        $mailer->Password   = 'ldmn gohy wqxm qvni';
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mailer->Port       = 465;

        //configuration du mail
        $mailer->setFrom('easyrepair@gmail.com', 'Easy Repair');
        $mailer->addAddress($user->getEmail());

        $mailer->isHTML(true);
        $mailer->Subject = 'Confirmation de votre inscription';
        $mailer->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de votre inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 5px;
            overflow: hidden;
        }
        .email-header {
            background: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 20px;
        }
        .email-body p {
            margin: 0 0 15px;
        }
        .email-code {
            display: inline-block;
            background: #f4f4f4;
            color: #333;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 4px;
            border: 1px solid #dddddd;
            margin: 20px 0;
        }
        .email-footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #555;
        }
        .email-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Bienvenue sur Easy Repair</h1>
        </div>
        <div class="email-body">
            <p>Bonjour <strong>' . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . '</strong>,</p>
            <p>Merci de vous être inscrit sur notre site Easy Repair. Veuillez entrer ce code pour valider votre inscription :</p>
            <div class="email-code">' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '</div>
            <p>Nous vous remercions de votre confiance et vous souhaitons une excellente expérience sur notre site.</p>
            <p>Cordialement,</p>
            <p>L\'équipe Easy Repair</p>
        </div>
        <div class="email-footer">
            <p>Vous avez des questions ? <a href="https://www.easyrepair.com/contact">Contactez-nous</a>.</p>
            <p>&copy; ' . date("Y") . ' Easy Repair. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>';


        return $mailer->send();

    } catch (\Throwable $th) {
        throw $th;
    }
}
