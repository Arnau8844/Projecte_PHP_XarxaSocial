<?php
    require_once './bd/bd_connection.php';
    require_once './bd/bd_functions.php';
    require_once './mail/mail.php';
    $user = false;

if (isset($_GET['code']) && isset($_GET['mail'])) {
    $code = $_GET['code'];
    $mail = $_GET['mail'];

    $user = checkTimePassRestart($code , $mail);

    if (!$user)
    {
        die("El enlace ha caducado o es inválido.");
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    if (updatePassword($hashed_password, $mail)) {

        echo "<script>alert('Contraseña restablecida con éxito.'); window.location.href='home.php';</script>";

    } else {
        echo "<script>alert('Error al restablecer la contraseña. Intenta nuevamente.'); window.history.back();</script>";

    }

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body class="d-flex vh-100 justify-content-center align-items-center body">

    <div class="login-container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="form-container">
            <h1 class="text-center mb-4">Restablecer Contraseña</h1>
            <div class="text-center">
                <img src="./imgs/logo.jpg" alt="logo" class="logo-img" style="width: auto; height: 20vh;">
            </div>
            <form method="POST" class="form-signin">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">
                <input type="hidden" name="mail" value="<?php echo htmlspecialchars($mail); ?>">
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña:</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
