<?php
require_once './bd/bd_connection.php';
require_once './bd/bd_functions.php'; // Ahora usamos insertPost desde aquí
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para publicar.']);
        exit;
    }

    $iduser = $_SESSION['user']['id'];
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    $image = $video_url = $link = null;

    // Validar que se incluya contenido escrito
    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'El contenido no puede estar vacío.']);
        exit;
    }

    // Procesar imagen (opcional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Procesar video (opcional)
    if (!empty($_POST['video_url'])) {
        $video_url = filter_input(INPUT_POST, 'video_url', FILTER_SANITIZE_URL);
        if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'La URL del video no es válida.']);
            exit;
        }
    }

    // Procesar enlace (opcional)
    if (!empty($_POST['link'])) {
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'La URL del enlace no es válida.']);
            exit;
        }
    }

    $content_type = 'text'; // por defecto, solo escrito
    if ($image && $video_url && $link) {
        $content_type = 'text+image+video+link';
    } elseif ($image && $video_url) {
        $content_type = 'text+image+video';
    } elseif ($image && $link) {
        $content_type = 'text+image+link';
    } elseif ($video_url && $link) {
        $content_type = 'text+video+link';
    } elseif ($image) {
        $content_type = 'text+image';
    } elseif ($video_url) {
        $content_type = 'text+video';
    } elseif ($link) {
        $content_type = 'text+link';
    }

    // Insertar el post en la base de datos
    $result = insertPost($iduser, $content_type, $content, $image, $video_url, $link);

    if ($result) {
        echo json_encode(['success' => true, 'message' => '¡Post publicado con éxito!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al publicar el post.']);
    }
    exit;
}
?>
