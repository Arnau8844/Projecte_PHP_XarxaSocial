<?php
    require_once './bd/bd_functions.php';
    session_start();
    $error      = false;
    $registrado = false;
    $mail       = "";

    if (!empty($_SESSION['user']['mail'])) {
        
        $mail = $_SESSION['user']['mail'];
        
        if (activatedUser($mail)) {
            header("Location: ./pages/home.php");

            exit;
        } else {

            session_destroy();
            $error = "El teu compte encara no està actiu. Si us plau, verifica el teu correu.";

        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

        $mail        = isset($_POST["mail"]) ? htmlspecialchars(trim($_POST["mail"])) : "";
        $contrasenya = isset($_POST["contrasenya"]) ? htmlspecialchars(trim($_POST["contrasenya"])) : "";

        if (! empty($mail) && ! empty($contrasenya)) {

            // Se asume que loginUser devuelve un array con los datos del usuario o false en caso de error
            $usuario = loginUser($mail, $contrasenya);

            if ($usuario) {

                if (activatedUser($mail)) {

                    $_SESSION['user'] = [
                        'id'       => $usuario['iduser'],
                        'username' => $usuario['username'],
                        'mail'     => $usuario['mail'],
                    ];

                    header("Location: ./pages/home.php");
                    exit;

                } else {
                    $error = "El teu compte encara no està actiu. Si us plau, verifica el teu correu.";
                }
            } else {
                $error = "Usuari o contrasenya incorrectes.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="body">

<?php if ($error): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '<?php echo addslashes(htmlspecialchars($error, ENT_QUOTES, "UTF-8")); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f8d7da',
                color: '#721c24',
                customClass: {
                    popup: 'small-toast'
                }
            });
        });
    </script>
<?php endif; ?>

<div class="login-container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="form-container">
        <h1 class="text-center mb-4">Inici de Sessió</h1>
        <div class="text-center">
            <img src="./imgs/logo-v2-removebg-preview (1).jpg" alt="logo" class="logo-img" style="width: auto; height: 20vh;">
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="form-signin">
            <div class="mb-3">
                <label for="mail" class="form-label">Usuari o Correu:</label>
                <input type="text" name="mail" class="form-control" id="mail" placeholder="Introdueix el teu mail" required>
            </div>
            <div class="mb-3">
                <label for="contrasenya" class="form-label">Contrasenya:</label>
                <input type="password" name="contrasenya" class="form-control" id="contrasenya" placeholder="Introdueix la teva contrasenya" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn">Entrar</button>
            </div>
            <div class="mt-3 text-center">
                <a href="./pages/register.php" class="links">No tens compte encara?</a>
            </div>
        </form>
    </div>
</div>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.querySelector('.toast');
        if (toastEl) {
            setTimeout(function () {
                var toast = new bootstrap.Toast(toastEl);
                toast.hide();
            }, 5000);
        }
    });
</script>

</body>
</html>
