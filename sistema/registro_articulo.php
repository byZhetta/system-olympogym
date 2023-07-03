<?php 

    include "../conexion.php"; 
    session_start();   

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['proveedor']) || empty($_POST['descripcion']) || empty($_POST['marca']) ||
           empty($_POST['cantidad']) || empty($_POST['precio']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $proveedor   = $_POST['proveedor'];
            $descripcion = $_POST['descripcion'];
            $marca       = $_POST['marca'];
            $cantidad    = $_POST['cantidad'];
            $precio      = $_POST['precio'];

            $query_insert = mysqli_query($conexionDB,"INSERT INTO articulos(Descripcion,Cantidad,Precio_Unitario,Marca,Cod_Proveedor)
                                                        VALUES('$descripcion','$cantidad','$precio','$marca','$proveedor')");

            if($query_insert){
                $alert = '<p class="msg_save">Artículo guardado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al guardar el artículo.</p>';
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title><?php echo $nombreGym ?> | Registro Artículo</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Registro de Artículo</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="proveedor">Proveedor</label>
                <?php
                    $query_proveedor = mysqli_query($conexionDB,"SELECT IdProveedor, Nombre FROM proveedores ORDER BY Nombre ASC");
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                    mysqli_close($conexionDB);
                ?>
                <select name="proveedor" id="proveedor">
                    <option value="0"></option>
                <?php
                    if($result_proveedor > 0){
                        while ($proveedor = mysqli_fetch_array($query_proveedor)){
                ?>
                    <option value="<?php echo $proveedor['IdProveedor']; ?>"><?php echo $proveedor['Nombre']; ?></option>
                <?php
                        }
                    }
                ?>
                </select>
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" placeholder="Descripción del artículo">
                <label for="marca">Marca</label>
                <input type="text" name="marca" id="marca" placeholder="Ingrese la marca del artículo">
                <label for="cantidad">Cantidad</label>
                <input type="number" name="cantidad" id="cantidad" placeholder="Ingrese una cantidad">
                <label for="precio">Precio</label>
                <input value="" type="number" step="any" name="precio" id="precio" placeholder="Ingrese el precio unitario"><br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Guardar Artículo</button>
                <a href="lista_articulos.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>