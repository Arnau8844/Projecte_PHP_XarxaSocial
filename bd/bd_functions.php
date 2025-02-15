<?php

// use PDO;
// use PDOException;

// function connBD(){
//     $cadenaConnexio = 'mysql:dbname=xarxasocial_bd;host=localhost:port=3335';
//     $usuari = 'root';
//     $passwd = '';
//     try {

//         $db = new PDO($cadenaConnexio, $usuari, $passwd,
//             array(PDO::ATTR_PERSISTENT => true));

//         if ($db != null) {
//             // echo '<pre>';
//             // echo "Connexió establerta! \n ";
//             // echo '</pre>';
//             echo 'Connexió establerta!<br>';
//         }

//     } catch (PDOException $e) {
//         echo 'Error amb la BDs: ' . $e->getMessage() . '<br>';
//     }
// }
require_once './bd/bd_connection.php';

function loginUser($input, $contrasenya)
{
    require_once './bd/bd_functions.php';

    try {
        $db = connectarBD();

        // Verificar si el input es un email o username
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $campo = 'mail';
        } else {
            $campo = 'username';
        }

        // Preparar la consulta segura con PDO
        $sql  = "SELECT iduser, passHash FROM users WHERE $campo = :input";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':input', $input, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar la contraseña
            if (password_verify($contrasenya, $usuario['passHash'])) {
                return true; // Inicio de sesión correcto
            }
        }

        return false; // Usuario o contraseña incorrectos
    } catch (PDOException $e) {
        error_log("Error en loginUser: " . $e->getMessage());
        return false;
    }
}

function insertUsuariBD(
    string $mail,
    string $username,
    string $password,
    string $userFirstName,
    string $userLastName,
    ?string $activationDate,
    ?string $activationCode,
    ?string $resetPassExpiry,
    ?string $resetPassCode,
    ?string $removeDate,
    ?string $lastSignIn
): bool {
    try {
        $db = connectarBD();

        $sql = "INSERT INTO users (
                    mail,
                    username,
                    passHash,
                    userFirstName,
                    userLastName,
                    creationDate,
                    removeDate,
                    lastSignIn,
                    active,
                    activationDate,
                    activationCode,
                    resetPassExpiry,
                    resetPassCode
                ) VALUES (
                    :mail,
                    :username,
                    :password,
                    :userFirstName,
                    :userLastName,
                    NOW(),
                    :removeDate,
                    :lastSignIn,
                    :active,
                    :activationDate,
                    :activationCode,
                    :resetPassExpiry,
                    :resetPassCode
                )";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':userFirstName', $userFirstName, PDO::PARAM_STR);
        $stmt->bindParam(':userLastName', $userLastName, PDO::PARAM_STR);
        $stmt->bindParam(':removeDate', $removeDate, $removeDate ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':lastSignIn', $lastSignIn, $lastSignIn ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $active = 0;
        $stmt->bindParam(':active', $active, PDO::PARAM_INT);
        $stmt->bindParam(':activationDate', $activationDate, $activationDate ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':activationCode', $activationCode, $activationCode ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':resetPassExpiry', $resetPassExpiry, $resetPassExpiry ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':resetPassCode', $resetPassCode, $resetPassCode ? PDO::PARAM_STR : PDO::PARAM_NULL);

        return $stmt->execute();

    } catch (PDOException $e) {
        error_log('Error en la BD: ' . $e->getMessage());
        return false;
    }
}

function activatedUser($mail): bool
{
    try {
        $db = connectarBD();
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $campo = 'mail';
        } else {
            $campo = 'username';
        }
        $query = $db->prepare("SELECT active FROM users WHERE $campo = :mail");
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        return $row && $row['active'] == 1;
    } catch (PDOException $e) {
        error_log("Error en activatedUser: " . $e->getMessage());
        return false;
    }
}

function getUserFromInput($usernameOrEmail)
{
    try {
        $db = connectarBD();

        $stmt = $db->prepare("SELECT iduser, mail FROM users WHERE mail = :mail OR username = :username LIMIT 1");
        $stmt->execute(['mail' => $usernameOrEmail, 'username' => $usernameOrEmail]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return [
                'iduser' => (int) $user['iduser'],
                'mail'   => $user['mail'],
            ];
        }
    } catch (PDOException $e) {
        error_log('Error con la BD: ' . $e->getMessage());
    }

    return null;
}

function activateUser($mail, $activationCode)
{
    try {
        $db = connectarBD();

        $selectQuery = $db->prepare("SELECT iduser FROM users WHERE mail = :email AND activationCode = :code AND active = 0");
        $selectQuery->bindParam(':email', $mail, PDO::PARAM_STR);
        $selectQuery->bindParam(':code', $activationCode, PDO::PARAM_STR);
        $selectQuery->execute();

        if ($selectQuery->rowCount() > 0) {
            $updateQuery = $db->prepare("UPDATE users SET active = 1, activationCode = NULL, activationDate = NOW() WHERE mail = :email AND activationCode = :code AND active = 0");
            $updateQuery->bindParam(':email', $mail, PDO::PARAM_STR);
            $updateQuery->bindParam(':code', $activationCode, PDO::PARAM_STR);

            if ($updateQuery->execute() && $updateQuery->rowCount() > 0) {
                return true;
            }
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error en activateUser: " . $e->getMessage());
        return false;
    }
}

function actualizarResetPassword(int $userId)
{
    try {

        $db = connectarBD();

        $resetCode = bin2hex(random_bytes(32));
        $resetExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $stmt = $db->prepare("UPDATE users SET resetPassCode = :resetCode, resetPassExpiry = :resetExpiry WHERE iduser = :iduser");
        $stmt->execute(['resetCode' => $resetCode, 'resetExpiry' => $resetExpiry, 'iduser' => $userId]);

        return $resetCode;
    } catch (PDOException $e) {
        error_log('Error al actualizar código de recuperación: ' . $e->getMessage());
        return null;
    }
}

function checkTimePassRestart($code, $mail)
{
    try {
        $db = connectarBD();

        $stmt = $db->prepare(
            "SELECT iduser, resetPassExpiry, resetPassCode
            FROM users
            WHERE mail = :mail"
        );
        $stmt->execute(['mail' => $mail]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $expiryDate = new DateTime($user['resetPassExpiry']);
            $currentDate = new DateTime();

            if ($expiryDate > $currentDate && $user['resetPassCode'] === $code) {
                return $user; 
            } else {
                echo "El código ha expirado.";
                return null;
            }
        } else {
            echo "No se encontró el código de recuperación o el correo.";
            return null;
        }

    } catch (PDOException $e) {

        error_log('Error al verificar el código de recuperación: ' . $e->getMessage());
        echo "Error de base de datos: " . $e->getMessage();
        return null;
    }
}



function updatePassword($hashed_password, $mail)
{
    try {

        $db = connectarBD();

        $stmt = $db->prepare("UPDATE users SET passHash = :passHash, resetPassCode = NULL, resetPassExpiry = NULL WHERE mail = :mail");
        $stmt->execute(['passHash' => $hashed_password, 'mail' => $mail]);

        if ($stmt->rowCount() > 0) {
            echo "Contraseña actualizada correctamente. Puedes iniciar sesión.";
        } else {
            echo "Error al actualizar la contraseña.";
        }

        return true;

    } catch (PDOException $e) {
        error_log('Error al actualizar la contraseña: ' . $e->getMessage());
        return false;
    }
}

