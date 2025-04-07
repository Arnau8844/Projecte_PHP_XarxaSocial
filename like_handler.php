<?php
require_once __DIR__ . '/../bd/bd_functions.php';
session_start();

if (empty($_SESSION['user'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['post_id'])) {
    echo json_encode(['error' => 'ID del post no especificado']);
    exit;
}

$post_id = intval($_POST['post_id']);

// Se asume que la función addLike en bd_functions.php incrementa el contador de likes y retorna el nuevo total.
$newLikes = addLike($post_id);

if ($newLikes !== false) {
    echo json_encode(['likes' => $newLikes]);
} else {
    echo json_encode(['error' => 'Error al actualizar el like']);
}
?>
