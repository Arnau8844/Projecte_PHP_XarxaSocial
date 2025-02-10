<?php
    require_once './bd/bd_connection.php';
    require_once './bd/bd_functions.php';

    session_start();
    $error      = false;
    $registrado = false;
    $db         = connectarBD();
    $mail       = " ";

    if (isset($_SESSION['user'])) {

        $mail = $_SESSION['user']['mail'];

        $query = $db->prepare("SELECT active FROM users WHERE mail = :email");
        $query->bindParam(':email', $mail, PDO::PARAM_STR);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['active'] == 1) {

                header("Location: home.php");
                exit;

            } else {
                $error = "Activa el teu compte Siusplau.";
                alert("<?php echo addslashes($error); ?>");
            }
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username  = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
        $mail     = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $name1     = filter_input(INPUT_POST, "name1", FILTER_SANITIZE_STRING);
        $name2     = filter_input(INPUT_POST, "name2", FILTER_SANITIZE_STRING);
        $pass      = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING);
        $verifPass = filter_input(INPUT_POST, "verifPass", FILTER_SANITIZE_STRING);

        $hashedPass = password_hash($pass, PASSWORD_BCRYPT);

        $result = insertUsuariBD($mail, $username, $hashedPass, $name1, $name2, date('Y-m-d H:i:s'), null, date('Y-m-d H:i:s'));
        if ($result) {
            $_SESSION['user'] = [
                'username' => $username,
                'mail'     => $mail,
                'active'   => 0,
            ];
            header("Location: index.php");
            exit;
        } else {
            $error = "Error al registrar l'usuari.";
        }
    }

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTRE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body class="body d-flex vh-100 justify-content-center align-items-center">
    <div>
        <!-- Logo -->
        <div class="text-center">
            <img src="./imgs/logo.jpg" alt="logo" style="height: 20vh;">
        </div>
        <!-- Formulario -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <!-- Campo Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'Usuari:</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="Nom d'Usuari" required>
            </div>
            <!-- Campo Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Correu Electrònic:</label>
                <input name="email" type="email" class="form-control" id="email" placeholder="Correu Electrònic" required>
            </div>
            <!-- Campo Primer Nombre -->
            <div class="mb-3">
                <label for="name1" class="form-label">Nom:</label>
                <input name="name1" type="text" class="form-control" id="name1" placeholder="Nom" required>
            </div>
            <!-- Campo Apellido -->
            <div class="mb-3">
                <label for="name2" class="form-label">Cognom:</label>
                <input name="name2" type="text" class="form-control" id="name2" placeholder="Cognom" required>
            </div>
            <!-- Campo Contraseña -->
            <div class="mb-3">
                <label for="pass" class="form-label">Contrasenya:</label>
                <input name="pass" type="password" class="form-control" id="pass" placeholder="Contrasenya" required>
            </div>
            <!-- Campo Verificar Contraseña -->
            <div class="mb-3">
                <label for="verifPass" class="label">Verifica la Contrasenya:</label>
                <input name="verifPass" type="password" class="form-control" id="verifPass" placeholder="Verifica la Contrasenya" required>
            </div>
            <!-- Botón de envío -->
            <div class="d-grid mb-5">
                <button type="submit" class="btn">Registrar-se</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
