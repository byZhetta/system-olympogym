<?php 

    include "../conexion.php";    

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['dias']) || empty($_POST['hora']) ||
            empty($_POST['duracion']) || empty($_POST['precio']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idClase   = $_POST['id'];
            $dias      = $_POST['dias'];
            $hora      = $_POST['hora'];
            $duracion  = $_POST['duracion'];
            $precio    = $_POST['precio'];

            $query_update = mysqli_query($conexionDB,"UPDATE clases SET Dias = '$dias',
                                                                        Hora = '$hora',
                                                                        Duracion = '$duracion',
                                                                        Costo_Clase = $precio
                                                                        WHERE IdClase = $idClase");

            if($query_update){
                $alert = '<p class="msg_save">Clase actualizada correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar la clase.</p>';
            }
        }
    }

    //validar producto
     //Mostrar Datos
     if(empty($_REQUEST['id'])){
        header('Location: clases.php');
        mysqli_close($conexionDB);
    }
    $idClase = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM clases WHERE IdClase = $idClase ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: clases.php');
    } else {

        while ($data = mysqli_fetch_array($sql)) {

            $idClase = $data['IdClase'];
            $nombreCla = $data['NombreC'];
            $dias = $data['Dias'];
            $hora = $data['Hora'];
            $duracion = $data['Duracion'];
            $precio = $data['Costo_Clase'];

        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Olympo gym | Sistema</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Actualizar clase</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idClase; ?>">
                Clase: <?php echo $nombreCla; ?>
                <label for="dias">Dias</label>
                <input type="text" name="dias" id="dias" placeholder="Ingrese los Dias ej. Lun, Mar, Mie, Jue, Vie, Sab" value="<?php echo $dias; ?>">
                <label for="hora">Hora</label>
                <input type="time" name="hora" id="hora" placeholder="Ingrese la Hora de Clase" value="<?php echo $hora; ?>">
                <label for="duracion">Duración</label>
                <input type="time" name="duracion" id="duracion" placeholder="Ingrese la Duración de Clase" value="<?php echo $duracion; ?>">
                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ingrese el precio" value="<?php echo $precio; ?>"><br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Actualizar Clase</button>
                <a href="clases.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>