<?php
require_once  './bd/bd_functions.php';

    session_start();
    $error      = false;
    $registrado = false;

    if (isset($_SESSION['user'])) {
        header("Location: home.php");
        exit;
    } else
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mail       = isset($_POST["mail"]) ? htmlspecialchars(trim($_POST["mail"])) : "";
        $contrasenya = isset($_POST["contrasenya"]) ? htmlspecialchars(trim($_POST["contrasenya"])) : "";

        if (! empty($mail) || ! empty($contrasenya)) {

            $registrado = usuarioRegistrado($mail, $contrasenya);
            if ($registrado) {
                $userData   = [
                    'mail'       => $mail,
                    'contrasenya' => $contrasenya,
                ];
                $_SESSION["user"] = $userData;
                header("Location: home.php");
            }
            else {
                $registrado = "Usuari o contrasenya incorrectes.";
            }
        }
    }
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
            <h1 class="text-center mb-4">Inici de Sessió</h1>
            <div class="text-center">
                <img src="./imgs/logo.jpg" alt="logo" class="logo-img " style="width: auto; height: 20vh;">
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="form-signin">
                <!-- Campo mail -->
                <div class="mb-3">
                    <label for="mail" class="form-label">Usuari o Correu:</label>
                    <input type="text" name="mail" class="form-control" id="mail" placeholder="Introdueix el teu mail" required>
                </div>
                <!-- Campo Contraseña -->
                <div class="mb-3">
                    <label for="contrasenya" class="form-label">Contrasenya:</label>
                    <input type="password" name="contrasenya" class="form-control" id="contrasenya" placeholder="Introdueix la teva contrasenya" required>
                </div>
                <!-- Botón de envío -->
                <div class="d-grid">
                    <button type="submit" class="btn">Entrar</button>
                </div>
                <!-- Enlace a registro -->
                <div class="mt-3 text-center">
                    <a href="./register.php" class="links">No tens compte encara?</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>