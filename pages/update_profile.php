<?php
require_once '../bd/bd_functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

$mail = $_SESSION['user']['mail'];
$user = getUserData($mail);

$username    = $_POST['username'] ?? $user['username'];
$firstName   = $_POST['firstName'] ?? $user['userFirstName'];
$lastName    = $_POST['lastName'] ?? $user['userLastName'];
$data_naix   = $_POST['data_naix'] ?? $user['data_naix'];
$location    = $_POST['location'] ?? $user['location'];
$description = $_POST['description'] ?? $user['description'];

// Se usa el avatar actual como valor predeterminado
$avatar = $user['avatar'];

// Procesa el archivo subido si existe
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/avatars/';
    $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    $validTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $validTypes)) {
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            $avatar = basename($_FILES['avatar']['name']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Problema al subir la imagen.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Formato de imagen no válido.']);
        exit;
    }
}

// Actualiza el perfil en la base de datos
$updateResult = updateUserProfile($mail, $username, $firstName, $lastName, $data_naix, $location, $description, $avatar);

if ($updateResult) {
    // Actualiza la sesión
    $_SESSION['user']['username']    = $username;
    $_SESSION['user']['avatar']      = $avatar;
    $_SESSION['user']['location']    = $location;
    $_SESSION['user']['description'] = $description;
    $_SESSION['user']['firstName']   = $firstName;
    $_SESSION['user']['lastName']    = $lastName;
    $_SESSION['user']['data_naix']   = $data_naix;
    
    echo json_encode(['success' => true, 'avatar' => $avatar]);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar los cambios.']);
    exit;
}
?>
