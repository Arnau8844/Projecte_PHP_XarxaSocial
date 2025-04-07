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

function connectarBD()
{
    $cadenaConnexio = 'mysql:dbname=xarxasocial_bd;host=localhost;port=3335';
    $usuari         = 'root';
    $passwd         = '';
    $db             = null;

    try {
        $db = new PDO($cadenaConnexio, $usuari, $passwd,
            array(PDO::ATTR_PERSISTENT => true));
            
    } catch (PDOException $e) {
        echo 'Error amb la BDs: ' . $e->getMessage() . '<br>';
    }

    return $db;
}    

function loginUser($input, $contrasenya)
{

    try {
        $db = connectarBD();

        // Verificar si el input es un email o username
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $campo = 'mail';
        } else {
            $campo = 'username';
        }

        // Preparar la consulta segura con PDO
        $sql  = "SELECT * FROM users WHERE $campo = :input";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':input', $input, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar la contraseña
            if (password_verify($contrasenya, $usuario['passHash'])) {
                return $usuario; // Inicio de sesión correcto
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
) {
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

        if ($stmt->execute()) {
            // Convertir a entero para asegurar la correcta vinculación en futuras consultas
            $lastId = (int)$db->lastInsertId();
            return $lastId;
        } else {
            return false;
        }
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

function getUserData($usernameOrEmail)
{
    try {
        $db = connectarBD();

        // 🔹 1. Obtener los datos del usuario
        $stmt = $db->prepare("SELECT * FROM users WHERE mail = :mail OR username = :username LIMIT 1");
        $stmt->execute(['mail' => $usernameOrEmail, 'username' => $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("Usuario no encontrado: $usernameOrEmail");
            return null;
        }

        $userId = $user['iduser'];

        if (!$userId) {
            error_log("Error: iduser es NULL para $usernameOrEmail");
            return null;
        }

        // 🔹 2. Obtener número de seguidores
        $stmt = $db->prepare("SELECT COUNT(*) AS followers FROM followers WHERE id_following = :userId");
        $stmt->execute(['userId' => $userId]);
        $followers = $stmt->fetch(PDO::FETCH_ASSOC)['followers'] ?? 0;

        // 🔹 3. Obtener número de seguidos
        $stmt = $db->prepare("SELECT COUNT(*) AS following FROM followers WHERE id_follower = :userId");
        $stmt->execute(['userId' => $userId]);
        $following = $stmt->fetch(PDO::FETCH_ASSOC)['following'] ?? 0;

        // 🔹 4. Obtener número de publicaciones
        $stmt = $db->prepare("SELECT COUNT(*) AS publicationsCount FROM publications WHERE iduser = :userId");
        $stmt->execute(['userId' => $userId]);
        $publicationsCount = $stmt->fetch(PDO::FETCH_ASSOC)['publicationsCount'] ?? 0;

        // 🔹 5. Agregar datos al array del usuario
        $user['followers'] = $followers;
        $user['following'] = $following;
        $user['publicationsCount'] = $publicationsCount;

        return $user;

    } catch (PDOException $e) {
        error_log("Error con la BD: " . $e->getMessage());
        return null;
    }
}

function updateUserProfile($mail, $username, $firstName, $lastName, $data_naix, $location, $description, $avatar) {
    
    try {
        
        $db = connectarBD();

        $sql = "UPDATE users SET 
                    mail = :mail,
                    username = :username, 
                    userFirstName = :userFirstName, 
                    userLastName = :userLastName, 
                    data_naix = :data_naix, 
                    location = :location, 
                    description = :description, 
                    avatar = :avatar
                WHERE mail = :mail";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'mail' => $mail,
            'username' => !empty($username) ? $username : null,
            'userFirstName' => !empty($firstName) ? $firstName : null,
            'userLastName' => !empty($lastName) ? $lastName : null,
            'data_naix' => !empty($data_naix) ? $data_naix : null,
            'location' => !empty($location) ? $location : null,
            'description' => !empty($description) ? $description : null,
            'avatar' => !empty($avatar) ? $avatar : null
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    } catch (PDOException $e) {
        error_log("Error con la BD: " . $e->getMessage());
        return false;
    }
}

function insertPost($iduser, $content_type, $content, $image, $video_url) {
    try {
        $db = connectarBD();
        $sql = "INSERT INTO publications (iduser, content_type, content, image, video_url, likes)
                VALUES (:iduser, :content_type, :content, :image, :video_url, :likes)";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':iduser', $iduser, PDO::PARAM_INT);
        $stmt->bindValue(':content_type', $content_type, PDO::PARAM_STR);
        $stmt->bindValue(':content', $content, PDO::PARAM_STR);
        
        // Asignar valor para 'image'
        if (!empty($image)) {
            $stmt->bindValue(':image', $image, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':image', null, PDO::PARAM_NULL);
        }
        
        // Asignar valor para 'video_url'
        if (!empty($video_url)) {
            $stmt->bindValue(':video_url', $video_url, PDO::PARAM_STR);
        } else {
            $stmt->bindValue(':video_url', null, PDO::PARAM_NULL);
        }
        
        // Fijamos likes a 0
        $stmt->bindValue(':likes', 0, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error al insertar post: " . $e->getMessage());
        error_log("Detalles: " . implode(" - ", $stmt->errorInfo()));
        return false;
    }
}

function getPosts() {
    try {
        $db = connectarBD();
        $sql = "SELECT p.*, u.username 
                FROM publications p 
                JOIN users u ON p.iduser = u.iduser 
                ORDER BY p.created_date DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener posts: " . $e->getMessage());
        return [];
    }
}

function addLike($post_id, $user_id) {
    try {
        $db = connectarBD();

        // Verificar si el usuario ya le dio like a esta publicación
        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM likes WHERE publication_id = :post_id AND user_id = :user_id");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            // El usuario ya le dio like, simplemente retornar el contador actual
            $stmt = $db->prepare("SELECT likes FROM publications WHERE id = :post_id");
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['likes'] ?? false;
        }

        // Insertar el like en la tabla intermedia
        $stmt = $db->prepare("INSERT INTO likes (user_id, publication_id) VALUES (:user_id, :post_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Actualizar el contador de likes en la publicación
            $stmt = $db->prepare("UPDATE publications SET likes = likes + 1 WHERE id = :post_id");
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();

            // Obtener el nuevo total de likes
            $stmt = $db->prepare("SELECT likes FROM publications WHERE id = :post_id");
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['likes'] ?? false;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Error en addLike: " . $e->getMessage());
        return false;
    }
}

function getSuggestedUsers($currentUserId, $limit = 5) {
    try {
        $db = connectarBD(); // usa tu función para conectar a la base de datos

        $sql = "SELECT iduser, username, avatar 
                FROM users 
                WHERE iduser != :currentUserId 
                ORDER BY RAND() 
                LIMIT :limit";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error al obtener sugerencias: " . $e->getMessage());
        return [];
    }
}

