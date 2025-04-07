<?php
require_once '../bd/bd_functions.php';
session_start();

$error = false;
$registrado = false;

if (isset($_GET['token']) && isset($_GET['mail'])) {
    $activationCode = $_GET['token'];
    $mail = $_GET['mail'];

    if (activateUser($mail, $activationCode)) {
        header("Location: home.php");
        exit;
    } else {
        echo "Error en activar el compte. Comprova que l'enllaç sigui correcte.";
    }
}

?>