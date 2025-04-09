<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

function enviarMail($destinatario, $asunto, $mensaje)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host       = 'smtp.gmail.com';
        $mail->Port       = 587;

        $mail->Username = 'arnau.mateuf@educem.net';
        $mail->Password = 'wdog xxru dhfz dpfy';

        $mail->setFrom('arnau.mateuf@educem.net', 'Arnau');
        $mail->addAddress($destinatario);
        $mail->Subject = $asunto;
        $mail->msgHTML($mensaje);

        if ($mail->send()) {
            return true;
        } else {
            return false;
        }

    } catch (Exception $e) {
        error_log('Error enviando correo: ' . $mail->ErrorInfo);
        return false;
    }
}

function enviarCorreoActivacion($mail, $token)
{
    $subject        = "Confirma el teu compte";
    $activationLink = "http://localhost/Projecte_PHP_xarxaSocial/pages/mailCheckAccount.php?token=$token&mail=$mail";
    
    $body           = "
        <p>Hola,</p>
        <p>Gràcies per registrar-te. Per confirmar el teu compte, fes clic al següent enllaç:</p>
        <p><a href='$activationLink' onclick='this.style.display=\"none\"'>Activa ja el teu compte!</a></p>
        <p>Si no vas sol·licitar aquest registre, ignora aquest missatge.</p>
        <img src='http://localhost/Projecte_PHP_xarxaSocial/imgs/logo-gran-v2.svg' alt='Imatge de confirmació'/>

    ";

    return enviarMail($mail, $subject, $body);
}
