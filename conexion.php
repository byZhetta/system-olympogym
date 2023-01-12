<?php

    // require __DIR__ . '/vendor/autoload.php';

    // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    // $dotenv->load();

    try {
        $conexionDB = new mysqli($_ENV['HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
        if ($conexionDB->connect_error){
            die("Ocurrió un error al conectar la base de datos!");
        }
    }
    catch (Exception $ex){
        echo "Ocurrió un error al conectarse a la base de datos!".$ex->getMessage();
    }

?>