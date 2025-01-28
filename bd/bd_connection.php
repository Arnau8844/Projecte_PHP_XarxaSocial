<?php

function connectarBD()
{
    $cadenaConnexio = 'mysql:dbname=xarxasocial_bd;host=localhost;port=3335';
    $usuari         = 'root';
    $passwd         = '';
    $db             = null;

    try {
        //Creem una connexió persistent a BDs
        $db = new PDO($cadenaConnexio, $usuari, $passwd,
            array(PDO::ATTR_PERSISTENT => true));
            
    } catch (PDOException $e) {
        echo 'Error amb la BDs: ' . $e->getMessage() . '<br>';
    }

    return $db;
}    