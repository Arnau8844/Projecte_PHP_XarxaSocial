<?php
require_once './bd/bd_connection.php';
require_once './bd/bd_functions.php';
require_once './mail/mail.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $usernameOrEmail = $_GET['mail'];

    $user = getUserFromInput($usernameOrEmail);

    if ($user) {
        $resetCode   = " ";

        $resetCode = actualizarResetPassword($user['iduser']);

        $subject = "Restablecimiento de contraseña";

        $resetLink = "http://localhost/Projecte_PHP_xarxaSocial/Projecte_PHP_XarxaSocial/resetPassword.php?code=" . $resetCode . "&mail=" . $user['mail'];

        $message = "Hola,\n\n";
        $message .= "Recibimos una solicitud para restablecer tu contraseña.\n";
        $message .= "Haz clic en el siguiente enlace para proceder:\n";
        $message .= "$resetLink\n\n";
        $message .= "Si no solicitaste este cambio, puedes ignorar este mensaje.\n\n";
        $message .= "Saludos,\nEl equipo de Soporte.";

        $headers = "From: Soporte <no-reply@tudominio.com>\r\n";
        $headers .= "Reply-To: soporte@tudominio.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (enviarMail($user['mail'], $subject, $message, $headers)) {
            echo "<script>alert('Se ha enviado un correo con las instrucciones para restablecer la contraseña.'); window.location.href='home.php';</script>";
        } else {
            echo "<script>alert('Error al enviar el correo. Intenta nuevamente.'); window.history.back();</script>";
        }
        echo "Correo enviado con éxito.";
        exit;
    } 
}