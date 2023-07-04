<?php 

    include "../conexion.php";  
    session_start();  

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['direccion']) || empty($_POST['telefono']) || 
        empty($_POST['correo']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre    = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $telefono  = $_POST['telefono'];
            $email     = $_POST['correo'];

            $query_insert = mysqli_query($conexionDB,"INSERT INTO proveedores(Nombre,Direccion,Telefono,Email)
                                                        VALUES('$nombre','$direccion','$telefono','$email')");

            if($query_insert){
                $alert = '<p class="msg_save">Proveedor guardado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al guardar el proveedor.</p>';
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
    <?php include "includes/texto.php"; ?>
	<title><?php echo $nombreGym ?> | Registro Proveedor</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Registro de proveedor</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese el Nombre">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese un Teléfono">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico"><br> 
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Guardar Proveedor</button>
                <a href="lista_proveedores.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>