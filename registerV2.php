<?php
session_start();
$error = false;
$registrado = false;

if ($_SERVER["REQUEST_METHOD"] != "POST" && isset($_SESSION['user'])) {
    $userData = $_SESSION['user'];
    if (isset($userData['email']) && isset($userData['contrasenya'])) {
        $registrado = true;
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? htmlspecialchars(trim($_POST["email"])) : "";
    $contrasenya = isset($_POST["contrasenya"]) ? htmlspecialchars(trim($_POST["contrasenya"])) : "";

    if (!empty($email) || !empty($contrasenya)) {
        $registrado = true;
        $userData = [
            'email' => $email,
            'contrasenya' => $contrasenya
        ];
        $_SESSION["user"] = $userData;
    }
}
// if ($registrado) {
//     header("Location: principal.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INICI SESSIÓ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body class="body">
    <div class="login-container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="form-container">
            <h1 class="text-center mb-4"></h1>
            <img src="./imgs/logo.jpg" alt="logo" class="logoImg">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="form-signin">
                <!-- Campo Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Correu Electrònic:</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Introdueix el teu email" required>
                </div>
                <!-- Campo Contraseña -->
                <div class="mb-3">
                    <label for="contrasenya" class="form-label">Contrasenya:</label>
                    <input type="password" name="contrasenya" class="form-control" id="contrasenya" placeholder="Introdueix la teva contrasenya" required>
                </div>
                <!-- Botón de envío -->
                <div class="d-grid">
                    <button type="submit" class="btn">Enviar</button>
                </div>
                <!-- Enlace a registro -->
                <div class="mt-3 text-center">
                    <a href="./register.php" class="login-link">No tens compte encara?</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>