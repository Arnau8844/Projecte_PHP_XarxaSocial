<?php
require_once '../bd/bd_connection.php';
require_once '../bd/bd_functions.php';
session_start();

if (empty($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$mail = $_SESSION['user']['mail'];

if (!activatedUser($mail)) {
    echo "<script>alert('Tu cuenta no está activada. Por favor, verifica tu correo.'); window.location.href = '../index.php';</script>";
    exit;
}

if (!isset($conn) || $conn === null) {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}

$error = "";
$success = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = handlePostSubmission($_POST, $_FILES);

    if ($result['success']) {
        $success = $result['message'];
        header("Location: home.php");
        exit;
    } else {
        $error = $result['message'];
    }
}

// Función para manejar la validación e inserción del post
function handlePostSubmission($postData, $fileData) {
    global $conn, $iduser;

    // Validar tipo de contenido
    if (empty($postData['content_type'])) {
        return ['success' => false, 'message' => 'El tipo de contenido es obligatorio.'];
    }
    $content_type = $postData['content_type'];

    // Validar contenido
    $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_STRING);
    if (empty($content)) {
        return ['success' => false, 'message' => 'El contenido no puede estar vacío.'];
    }

    // Validar imagen o video según el tipo de contenido
    $image = $video_url = NULL;
    if ($content_type === "image" && isset($fileData["image"])) {
        if ($fileData["image"]["error"] === UPLOAD_ERR_OK) {
            $image = "uploads/" . basename($fileData["image"]["name"]);
            move_uploaded_file($fileData["image"]["tmp_name"], $image);
        } else {
            return ['success' => false, 'message' => 'Error al subir la imagen.'];
        }
    } elseif ($content_type === "video") {
        $video_url = filter_input(INPUT_POST, "video_url", FILTER_SANITIZE_URL);
        if (empty($video_url) || !filter_var($video_url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'La URL del video no es válida.'];
        }
    }

    // Insertar en la base de datos
    if (insertPost($iduser, $content_type, $content, $image, $video_url)) {
        return ['success' => true, 'message' => '¡Post publicado con éxito!'];
    } else {
        return ['success' => false, 'message' => 'Error al publicar el post.'];
    }
}

// Función para insertar el post en la base de datos
function insertPost($iduser, $content_type, $content, $image, $video_url) {
    global $conn;

    if (!isset($conn) || $conn === null) {
        return false; // Asegúrate de que la conexión esté disponible
    }

    $stmt = $conn->prepare("INSERT INTO posts (iduser, content_type, content, image, video_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $iduser, $content_type, $content, $image, $video_url);
    return $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container col-md-6">
        <h2 class="text-center">Crear un Post</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Tipo de contenido:</label>
                <select name="content_type" class="form-control" required>
                    <option value="text">Texto</option>
                    <option value="image">Imagen</option>
                    <option value="video">Video</option>
                    <option value="link">Enlace</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Contenido:</label>
                <textarea name="content" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Subir imagen:</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">URL del video:</label>
                <input type="url" name="video_url" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">Publicar</button>
        </form>
    </div>
</body>
</html>
