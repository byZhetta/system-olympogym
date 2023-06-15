<?php 

    include "../conexion.php"; 
    session_start();   

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) ||
           empty($_POST['duracion']) || empty($_POST['precio'])){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $duracion = $_POST['duracion'];
            $precio = $_POST['precio'];

            $query_insert = mysqli_query($conexionDB,"INSERT INTO clases(NombreC,Duracion,Costo_Clase)
                                                                VALUES('$nombre','$duracion','$precio')");

            if($query_insert){
                $alert = '<p class="msg_save">Clase creada correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al crear la clase.</p>';
            }
        }
        mysqli_close($conexionDB);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Olympo gym | Registro Clase</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Crear tipo de membresía</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre de la Membresía">
                <label for="duracion">Duración</label>
                <input type="text" name="duracion" id="duracion" placeholder="Ingrese la Duración de Clase">
                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ingrese el Precio">
                <br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Crear Membresia</button>
                <a href="clases.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>