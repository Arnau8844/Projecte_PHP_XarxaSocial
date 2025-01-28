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
    ?string $lastSignIn, 
    int $active = 1
): ?PDO {

    try {
        
        $db = connectarBD();

        $sql = "INSERT INTO `users` (
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
                    '$mail', 
                    '$username', 
                    ' $password ', 
                    '$userFirstName', 
                    '$userLastName', 
                    '$creationDate', 
                    " . ($removeDate ? "'$removeDate'" : "NULL") . ", 
                    " . ($lastSignIn ? "'$lastSignIn'" : "NULL") . ", 
                    $active
                )";

        $insert = $db->query($sql);

        if ($insert) { $result = $insert->rowCount(); }
    
    } catch (PDOException $e) { $result = null; }

    return $result;
}


function usuarioRegistrado(string $mail, string $contrasenya): bool
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
