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

function insertUsuariBD(
    string $mail, 
    string $username, 
    string $password, 
    string $userFirstName, 
    string $userLastName, 
    string $creationDate, 
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
                    active
                ) VALUES (
                    :mail, 
                    :username, 
                    :password, 
                    :userFirstName, 
                    :userLastName, 
                    :creationDate, 
                    :removeDate, 
                    :lastSignIn, 
                    :active
                )";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':userFirstName', $userFirstName, PDO::PARAM_STR);
        $stmt->bindParam(':userLastName', $userLastName, PDO::PARAM_STR);
        $stmt->bindParam(':creationDate', $creationDate, PDO::PARAM_STR);
        $stmt->bindParam(':removeDate', $removeDate, $removeDate ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':lastSignIn', $lastSignIn, $lastSignIn ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $active = 0;
        $stmt->bindParam(':active', $active, PDO::PARAM_INT);

        return $stmt->execute();
    
    } catch (PDOException $e) { 
        error_log('Error en la BD: ' . $e->getMessage());
        return false;
    }
}


function usuarioRegistrado(string $mail): bool
{
    $registrado = false;

    try {
        $db = connectarBD();

        $query = $db->prepare("SELECT iduser FROM users WHERE mail = :mail OR username = :mail");
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            $registrado = true;
        }
    } catch (PDOException $e) {
    
        error_log('Error amb la BDs: ' . $e->getMessage());
    }

    return $registrado;
}
