<?php
require_once '../bd/bd_functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$mail = $_SESSION['user']['mail'];
$user = getUserData($mail);

if (!$user['active']) {
    $error = "El teu compte encara no està actiu. Si us plau, verifica el teu correu.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $data_naix = $_POST['data_naix'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    
    $avatar = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/avatars/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $validTypes)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                $avatar = basename($_FILES['avatar']['name']);
            } else {
                $error = "Hubo un problema al subir la imagen.";
            }
        } else {
            $error = "Formato de archivo no válido. Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
        }
    }

    $updateResult = updateUserProfile($mail, $username, $firstName, $lastName, $data_naix, $location, $description, $avatar);
    
    if ($updateResult) {
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['avatar'] = $avatar;
        $_SESSION['user']['location'] = $location;
        $_SESSION['user']['description'] = $description;
        $_SESSION['user']['firstName'] = $firstName;
        $_SESSION['user']['lastName'] = $lastName;
        $_SESSION['user']['data_naix'] = $data_naix;
        header('Location: perfil.php');
        exit;
    } else {
        $error = "Hubo un error al guardar los cambios. Por favor, intenta de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="../styles/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container-fluid">
  <div class="row justify-content-center">

    <!-- Sidebar (igual que home) -->
    <div class="col-auto p-0 d-none d-md-flex">
      <div class="d-flex flex-column flex-shrink-0 border-end vh-100 sidebar align-items-center text-center justify-content-between py-4">
        <div class="w-100 d-flex flex-column align-items-center sidebar-content">
          <a href="./home.php" class="d-flex flex-column align-items-center mb-4">
            <img src="../imgs/logo-gran-v2.svg" alt="Glow-Up Logo" class="logo-full ">
            <img src="../imgs/mini-logo.svg" alt="Glow-Up Mini Logo" class="logo-mini ">
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
            <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar'] ?? 'avatars/default-avatar.png'); ?>" alt="Avatar" class="rounded-circle mb-2" width="60" height="60">
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
        <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="rounded-circle" width="26" height="26" style="object-fit: cover;">
      </a>
      <a href="./logout.php" title="Cerrar sesión" class="text-dark">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>

    <!-- Contenido del perfil -->
    <div class="col-12 col-md-10 col-lg-8 col-xl-6 px-3 px-md-4 px-lg-5 py-4 mt-5">
      <div class="perfil d-flex align-items-center gap-4 flex-column flex-md-row">
        <div class="avatar text-center">
          <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="avatar" class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
        </div>
        <div class="flex-grow-1">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <h2 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Editar Perfil</button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#configModal"><i class="fas fa-cog"></i></button>
          </div>
          <div class="d-flex gap-3 text-muted mb-2">
            <p class="mb-0"><strong><?php echo htmlspecialchars($user['publicationsCount']); ?></strong> <?php echo $user['publicationsCount'] == 1 ? 'Publicación' : 'Publicaciones'; ?></p>
            <p class="mb-0"><strong><?php echo htmlspecialchars($user['followers']); ?></strong> <?php echo $user['followers'] == 1 ? 'Seguidor' : 'Seguidores'; ?></p>
            <p class="mb-0"><strong><?php echo htmlspecialchars($user['following']); ?></strong> <?php echo $user['following'] == 1 ? 'Seguido' : 'Seguidos'; ?></p>
          </div>
          <div>
            <span class="fw-bold"><?php echo htmlspecialchars($user['userFirstName']) . " " . htmlspecialchars($user['userLastName']); ?></span>
            <?php if (!empty($user['location'])): ?>
              <p class="mb-1"><?php echo htmlspecialchars($user['location']); ?></p>
            <?php endif; ?>
            <p class="mb-0"><?php echo !empty($user['description']) ? htmlspecialchars($user['description']) : 'No hay descripción todavía.'; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include './modals.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script>
  function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
      const avatarPreview = document.getElementById('avatarPreview');
      avatarPreview.src = e.target.result;
    }
    if (file) reader.readAsDataURL(file);
  }
</script>
</body>
</html>
