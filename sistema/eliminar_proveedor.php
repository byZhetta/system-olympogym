<?php
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){      
        if(empty($_POST['idProveedor'])){
            header("location: lista_proveedores.php");
            mysqli_close($conexionDB);
        }
        $idProveedor = $_POST['idProveedor'];
        //$query_delete = mysqli_query($conexionDB,"UPDATE socios SET estatus = 0 WHERE Id_Socio = $idSocio");
        $query_delete = mysqli_query($conexionDB,"DELETE FROM proveedores WHERE IdProveedor = $idProveedor ");
        mysqli_close($conexionDB);
        if($query_delete){
            header("location: lista_proveedores.php");
        } else {
            echo "Error al eliminar";
        }
    }
    
    if(empty($_REQUEST['id']) ){
        header("location: lista_proveedores.php");
        mysqli_close($conexionDB);
    } else {

        $idProveedor = $_REQUEST['id'];

        $query = mysqli_query($conexionDB,"SELECT * FROM proveedores
                                            WHERE IdProveedor = $idProveedor ");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while ($data = mysqli_fetch_array($query)) {
                $nombre = $data['Nombre'];
                $correo = $data['Email'];
            }
        } else {
            header("location: lista_proveedores.php");
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
            <i class="fas fa-building fa-7x" style="color: #e66262"></i>
            <br>
            <h2>¿Está suguro de eliminar el siguiente registro?</h2>
            <p>Nombre del proveedor: <span><?php echo $nombre; ?></span></p>
            <p>Email: <span><?php echo $correo; ?></span></p>

            <form method="post" action="">
                <input type="hidden" name="idProveedor" value="<?php echo $idProveedor; ?>">
                <a href="lista_proveedores.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>