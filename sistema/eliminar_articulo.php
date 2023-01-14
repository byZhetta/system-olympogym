<?php
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){      
        if(empty($_POST['idArticulo'])){
            header("location: lista_articulos.php");
            mysqli_close($conexionDB);
        }
        $idArticulo = $_POST['idArticulo'];
        //$query_delete = mysqli_query($conexionDB,"UPDATE socios SET estatus = 0 WHERE Id_Socio = $idSocio");
        $query_delete = mysqli_query($conexionDB,"DELETE FROM articulos WHERE IdArticulo = $idArticulo ");
        mysqli_close($conexionDB);
        if($query_delete){
            header("location: lista_articulos.php");
        } else {
            echo "Error al eliminar";
        }
    }
    
    if(empty($_REQUEST['id']) ){
        header("location: lista_articulos.php");
        mysqli_close($conexionDB);
    } else {

        $idArticulo = $_REQUEST['id'];

        $query = mysqli_query($conexionDB,"SELECT * FROM articulos
                                            WHERE IdArticulo = $idArticulo ");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while ($data = mysqli_fetch_array($query)) {
                $descripcion = $data['Descripcion'];
                $precio    = $data['Precio_Unitario'];
            }
        } else {
            header("location: lista_articulos.php");
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
            <i class="fas fa-cubes fa-7x" style="color: #e66262"></i>
            <br>
            <h2>¿Está suguro de eliminar el siguiente registro?</h2>
            <p>Artículo: <span><?php echo $descripcion; ?></span></p>
            <p>Precio: $<span><?php echo $precio; ?></span></p>

            <form method="post" action="">
                <input type="hidden" name="idArticulo" value="<?php echo $idArticulo; ?>">
                <a href="lista_articulos.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>