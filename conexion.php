<?php

    try {
        $conexionDB = new mysqli("localhost", "root", "", "gimnasio");
        if ($conexionDB->connect_error){
            die("Ocurrió un error al conectar la base de datos!");
        }
    }
    catch (Exception $ex){
        echo "Ocurrió un error al conectarse a la base de datos!".$ex->getMessage();
    }

?>