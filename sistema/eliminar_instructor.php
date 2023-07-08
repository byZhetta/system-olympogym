<?php
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){      
        if(empty($_POST['idInstructor'])){
            header("location: lista_instructores.php");
            mysqli_close($conexionDB);
        }
        $idInstructor = $_POST['idInstructor'];
        //$query_delete = mysqli_query($conexionDB,"UPDATE socios SET estatus = 0 WHERE Id_Socio = $idSocio");
        $query_delete = mysqli_query($conexionDB,"DELETE FROM instructores WHERE Id_Instructor = $idInstructor ");
        mysqli_close($conexionDB);
        if($query_delete){
            header("location: lista_instructores.php");
        } else {
            echo "Error al eliminar";
        }
    }
    
    if(empty($_REQUEST['id']) ){
        header("location: lista_instructores.php");
        mysqli_close($conexionDB);
    } else {

        $idInstructor = $_REQUEST['id'];

        $query = mysqli_query($conexionDB,"SELECT * FROM instructores
                                            WHERE Id_Instructor = $idInstructor ");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while ($data = mysqli_fetch_array($query)) {
                $nombre = $data['Nombre'];
                $dni    = $data['Dni'];
                $correo = $data['Email'];
            }
        } else {
            header("location: lista_instructores.php");
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
    <?php include "includes/texto.php"; ?>
	<title><?php echo $nombreGym ?></title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	
	<section id="container">
		<div class="data_delete">
            <i class="fas fa-user-times fa-7x" style="color: #e66262"></i>
            <br>
            <h2>¿Está suguro de eliminar el siguiente registro?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Dni: <span><?php echo $dni; ?></span></p>
            <p>Email: <span><?php echo $correo; ?></span></p>

            <form method="post" action="">
                <input type="hidden" name="idInstructor" value="<?php echo $idInstructor; ?>">
                <a href="lista_instructores.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>