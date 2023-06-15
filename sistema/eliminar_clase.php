<?php
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){      
        if(empty($_POST['idClase'])){
            header("location: clases.php");
            mysqli_close($conexionDB);
        }
        $idClase = $_POST['idClase'];
        //$query_delete = mysqli_query($conexionDB,"UPDATE socios SET estatus = 0 WHERE Id_Socio = $idSocio");
        $query_delete = mysqli_query($conexionDB,"DELETE FROM clases WHERE IdClase = $idClase ");
        mysqli_close($conexionDB);
        if($query_delete){
            header("location: clases.php");
        } else {
            echo "Error al eliminar";
        }
    }
    
    if(empty($_REQUEST['id']) ){
        header("location: clases.php");
        mysqli_close($conexionDB);
    } else {

        $idClase = $_REQUEST['id'];

        $query = mysqli_query($conexionDB,"SELECT * FROM clases
                                            WHERE IdClase = $idClase ");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while ($data = mysqli_fetch_array($query)) {
                $nombre = $data['NombreC'];
                $duracion = $data['Duracion'];
                $costo = $data['Costo_Clase'];
            }
        } else {
            header("location: clases.php");
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
		<div class="data_delete">
            <i class="fas fa-dumbbell fa-7x" style="color: #e66262"></i>
            <br>
            <h2>¿Está suguro de eliminar el siguiente registro?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Duración: <span><?php echo $duracion; ?></span></p>
            <p>Costo: <span>S/.<?php echo $costo; ?></span></p>

            <form method="post" action="">
                <input type="hidden" name="idClase" value="<?php echo $idClase; ?>">
                <a href="clases.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>