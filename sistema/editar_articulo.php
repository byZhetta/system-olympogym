<?php 

    session_start();
    include "../conexion.php";    

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['descripcion']) || empty($_POST['marca']) ||
            empty($_POST['cantidad']) || empty($_POST['precio']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idarticulo  = $_POST['id'];
            $descripcion = $_POST['descripcion'];
            $marca       = $_POST['marca'];
            $cantidad    = $_POST['cantidad'];
            $precio      = $_POST['precio'];

            $query_update = mysqli_query($conexionDB,"UPDATE articulos SET Descripcion = '$descripcion',
                                                                            Marca = '$marca',
                                                                            Cantidad = $cantidad,
                                                                            Precio_Unitario = $precio
                                                                        WHERE IdArticulo = $idarticulo");

            if($query_update){
                $alert = '<p class="msg_save">Artículo actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el artículo.</p>';
            }
        }
    }

    //validar producto
     //Mostrar Datos
     if(empty($_REQUEST['id'])){
        header('Location: lista_articulos.php');
        mysqli_close($conexionDB);
    }
    $id_articulo = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM articulos WHERE IdArticulo = $id_articulo ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_articulos.php');
    } else {

        while ($data = mysqli_fetch_array($sql)) {

            $id_articulo = $data['IdArticulo'];
            $descripcion = $data['Descripcion'];
            $marca = $data['Marca'];
            $cantidad = $data['Cantidad'];
            $precio = $data['Precio_Unitario'];

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
            <h1>Actualizar artículo</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $id_articulo; ?>">
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" placeholder="Descripción del artículo" value="<?php echo $descripcion; ?>">
                <label for="marca">Marca</label>
                <input type="text" name="marca" id="marca" placeholder="Ingrese la marca del artículo" value="<?php echo $marca; ?>">
                <label for="cantidad">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" placeholder="Ingrese una cantidad" value="<?php echo $cantidad; ?>">
                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ingrese el precio unitario" value="<?php echo $precio; ?>"><br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Actualizar Artículo</button>
                <a href="lista_articulos.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>