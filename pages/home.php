<?php
    require_once '../bd/bd_functions.php';
    session_start();

    if (empty($_SESSION['user'])) {
        header("Location: ../index.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_like'])) {
        header('Content-Type: application/json');
        $post_id  = intval($_POST['post_id']);
        $newLikes = addLike($post_id, $_SESSION['user']['id']);

        if ($newLikes !== false) {
            echo json_encode(['likes' => $newLikes]);
        } else {
            echo json_encode(['error' => 'No se pudo actualizar el like.']);
        }
        exit;
    }
    $logedUser      = getUserData($_SESSION['user']['mail']);
    $posts          = getPosts();
    $suggestedUsers = getSuggestedUsers($_SESSION['user']['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Publicaciones</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="../styles/navbar.css">
  <link rel="stylesheet" href="../styles/home.css">

</head>
<body>
<?php include './modals.php'; ?>
<div class="container-fluid">
  <div class="row justify-content-center">

    <!-- Sidebar -->
    <div class="col-auto p-0 d-none d-md-flex">
      <div class="d-flex flex-column flex-shrink-0 border-end vh-100 sidebar align-items-center text-center justify-content-between py-4">
        <div class="w-100 d-flex flex-column align-items-center sidebar-content">
          <a href="./home.php" class="d-flex flex-column align-items-center mb-4">
            <img src="../imgs/logo-gran-v2.svg" alt="Glow-Up Logo" class="logo-full">
            <img src="../imgs/mini-logo.svg" alt="Glow-Up Mini Logo" class="logo-mini">
          </a>
          <ul class="nav nav-pills flex-column w-100 gap-2">
            <li class="nav-item">
              <a href="./home.php" class="nav-link text-dark fw-bold d-flex align-items-center justify-content-center flex-column">
                <i class="fas fa-home fs-4"></i><span>Inicio</span>
              </a>
            </li>
            <li>
              <a href="#" class="nav-link text-dark d-flex flex-column align-items-center" data-bs-toggle="modal" data-bs-target="#modalCrearPost">
                <i class="fas fa-pen fs-4"></i><span>Postear</span>
              </a>
            </li>
            <li>
              <a href="#" class="nav-link text-dark d-flex flex-column align-items-center" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                <i class="fas fa-key fs-4"></i><span>Restablecer</span>
              </a>
            </li>
            <li>
              <a href="./logout.php" class="nav-link d-flex flex-column align-items-center text-dark">
                <i class="fas fa-sign-out-alt fs-4"></i><span>Salir</span>
              </a>
            </li>
          </ul>
          <div class="text-center mt-4">
            <img src="../uploads/avatars/<?php echo htmlspecialchars($logedUser['avatar'] ?? 'avatars/default-avatar.png'); ?>" alt="Avatar" class="rounded-circle mb-2" width="60" height="60">
            <strong class="d-block text-dark"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Nav -->
    <div class="mobile-nav d-flex d-lg-none justify-content-around align-items-center">
      <a href="./home.php" title="Inicio">
        <i class="fas fa-home"></i>
      </a>
      <a href="#" data-bs-toggle="modal" data-bs-target="#modalCrearPost" title="Postear">
        <i class="fas fa-pen"></i>
      </a>
      <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" title="Restablecer contraseña">
        <i class="fas fa-key"></i>
      </a>
      <a href="./perfil.php" title="Perfil">
        <img 
          src="../uploads/avatars/<?php echo htmlspecialchars($logedUser['avatar'] ?? 'avatars/default-avatar.png'); ?>" 
          alt="Avatar" 
          class="rounded-circle" 
          width="26" height="26"
          style="object-fit: cover;"
        >
      </a>
      <a href="./logout.php" title="Cerrar sesión" class="text-dark">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>

    <!-- Contenido principal y sugerencias (centrado en pantallas sm y md) -->
    <div class="col-12 col-md-10 col-lg-8 col-xl-6 px-3 px-md-4 px-lg-5 py-4">
      <h1 class="mb-4">Bienvenido a Glow-Up ✨</h1>
      <!-- Posts -->
      <?php if (count($posts) > 0): ?>
<?php foreach ($posts as $post): ?>
          <div class="card my-3">
            <div class="card-header">
              <strong><?php echo htmlspecialchars($post['username']); ?></strong>
              <small class="text-muted ms-2"><?php echo htmlspecialchars($post['created_date']); ?></small>
            </div>
            <div class="card-body">
              <?php if (! empty($post['content'])): ?>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
              <?php endif; ?>
<?php if (! empty($post['image'])): ?>
                <img src="<?php echo "../" . htmlspecialchars($post['image']); ?>" class="img-fluid" alt="Imagen del post">
              <?php endif; ?>
<?php if (! empty($post['video_url'])): ?>
                <div class="ratio ratio-16x9 mt-3">
                  <iframe src="<?php echo "../uploads/" . htmlspecialchars($post['video_url']); ?>" allowfullscreen></iframe>
                </div>
              <?php endif; ?>
            </div>
            <div class="card-footer">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="likePost(<?php echo htmlspecialchars($post['id']); ?>)">
                    <i class="fa fa-thumbs-up"></i> Like
                  </button>
                  <button type="button" class="btn btn-outline-secondary btn-sm comment-btn" data-post-id="<?php echo htmlspecialchars($post['id']); ?>">
                    <i class="fa fa-comment"></i> Comment
                  </button>
                </div>
                <small class="text-muted">Likes: <span id="like-count-<?php echo htmlspecialchars($post['id']); ?>"><?php echo htmlspecialchars($post['likes']); ?></span></small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
<?php else: ?>
        <p class="text-center">No hay publicaciones para mostrar.</p>
      <?php endif; ?>
    </div>

<!-- Sugerencias a la derecha solo en XL -->
<div class="col-xl-3 d-none d-xl-block pt-4 px-3">
  <div class="sticky-top" style="top: 80px;">

    <!-- Usuario logueado -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="d-flex align-items-center">
        <img src="../uploads/avatars/<?php echo htmlspecialchars($logedUser['avatar'] ?? 'default-avatar.png'); ?>" class="rounded-circle me-2" width="48" height="48" style="object-fit: cover;">
        <strong class="small mb-0"><?php echo htmlspecialchars($logedUser['username']); ?></strong>
      </div>
      <a href="./perfil.php" class="btn btn-outline-primary btn-sm">Ver perfil</a>
    </div>

    <h6 class="text-muted mb-3">Sugerencias para ti</h6>
    <?php foreach ($suggestedUsers as $user): ?>
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center">
          <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar'] ?? 'default-avatar.png'); ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
          <strong class="small"><?php echo htmlspecialchars($user['username']); ?></strong>
        </div>
        <a href="#" class="btn btn-sm btn-outline-primary">Seguir</a>
      </div>
    <?php endforeach; ?>

    <div class="mt-4 text-muted small">
      <p class="text-muted">&copy; 2025 GLOW-UP</p>
    </div>
  </div>
</div>
    
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
function likePost(postId) {
  fetch('home.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'ajax_like=1&post_id=' + encodeURIComponent(postId)
  })
  .then(response => response.json())
  .then(data => {
    if (data.likes !== undefined) {
      document.getElementById('like-count-' + postId).textContent = data.likes;
    }
  });
}

document.querySelectorAll('.comment-btn').forEach(button => {
  button.addEventListener('click', function () {
    const postId = this.getAttribute('data-post-id');
    console.log("Comentar el post: " + postId);
  });
});
</script>
</body>
</html>
