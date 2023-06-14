<?php

    // require __DIR__ . '/vendor/autoload.php';

    // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    // $dotenv->load();

    // Variables de producción
    $HOST = "localhost";
    $DB_USER = "root";
    $DB_PASSWORD = "";
    $DB_NAME = "db_gym";

    try {
        $conexionDB = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
        if ($conexionDB->connect_error){
            die("Ocurrió un error al conectar la base de datos!");
        }
    }
    catch (Exception $ex){
        echo "Ocurrió un error al conectarse a la base de datos!".$ex->getMessage();
    }

?>