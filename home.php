<?php
require_once './mail/mail.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST['usernameOrEmail'])) {
        $to = trim($_POST['usernameOrEmail']);

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('El correo electrónico no es válido.'); window.history.back();</script>";
            exit;
        }

        header("Location: resetPasswordSend.php?mail=$to");
        exit;
        
    } else {
        echo "<script>alert('El campo no puede estar vacío.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar con Logo</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Enlace a tu CSS personalizado -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a href="./home.php" class="navbar-brand">
                <img src="./imgs/logo-blanc-alargado.svg" alt="Logo" style="width: auto; height: 10vh;">
            </a>

            <!-- Menú principal -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sobre nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>

            <div>

                <div class="btn-group">
                    <button class="navbar-toggler" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Inicio</a></li>
                        <li><a class="dropdown-item" href="#">Sobre nosotros</a></li>
                        <li><a class="dropdown-item" href="#">Servicios</a></li>
                        <li><a class="dropdown-item" href="#">Contacto</a></li>
                    </ul>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="fas fa-key"></i> Restablecer contraseña
                        </a>
                        <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                    </div>
                </div>

            </div>

        </div>
    </nav>

    <!-- Modal de Restablecer Contraseña -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="resetPasswordModalLabel">Restablecer Contraseña</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="mb-3">
                <label for="usernameOrEmail" class="form-label">Nombre de usuario o correo electrónico</label>
                <input type="text" class="form-control" id="usernameOrEmail" name="usernameOrEmail" placeholder="Introduce tu nombre de usuario o correo electrónico" required>
            </div>
            <button type="submit" class="btn btn-primary">Enviar correo de restablecimiento</button>
        </form>

        </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
