<?php 

    include "../conexion.php";    

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['hora']) || empty($_POST['dias']) ||
           empty($_POST['duracion']) || empty($_POST['precio']) || empty($_POST['instructor']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $dias = $_POST['dias'];
            $hora = $_POST['hora'];
            $duracion = $_POST['duracion'];
            $precio = $_POST['precio'];
            $instructor = $_POST['instructor'];

            $query_insert = mysqli_query($conexionDB,"INSERT INTO clases(Cod_Instructor,NombreC,Dias,Hora,Duracion,Costo_Clase)
                                                                VALUES('$instructor','$nombre','$dias','$hora','$duracion','$precio')");

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
            <h1>Crear Clase deportiva</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre de la Clase">
                <label for="dias">Dias</label>
                <input type="text" name="dias" id="dias" placeholder="Ingrese los Dias ej. Lun, Mar, Mie, Jue, Vie, Sab">
                <label for="hora">Hora</label>
                <input type="time" name="hora" id="hora" placeholder="Ingrese la Hora de Clase">
                <label for="duracion">Duración</label>
                <input type="time" name="duracion" id="duracion" placeholder="Ingrese la Duración de Clase">
                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ingrese el Precio">
                <label for="instructor">Instructor</label>
                
                <?php
                    $query_inst = mysqli_query($conexionDB,"SELECT * FROM instructores");
                    mysqli_close($conexionDB);
                    $result_inst = mysqli_num_rows($query_inst)
                ?>

                <select name="instructor" id="instructor">
                    <option value=""></option>
                    <?php
                        if($result_inst > 0){
                            while ($instructor = mysqli_fetch_array($query_inst)){
                    ?>         
                                <option value="<?php echo $instructor["Id_Instructor"]; ?>"><?php echo $instructor["Nombre"]; ?></option>
                    <?php            
                            }
                        }
                    ?>
                </select><br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Crear Clase</button>
                <a href="clases.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>