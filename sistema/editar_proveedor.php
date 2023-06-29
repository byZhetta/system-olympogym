<?php 
    session_start();
    include "../conexion.php";
    
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['direccion']) || empty($_POST['cp']) ||
            empty($_POST['telefono']) || empty($_POST['correo'])  ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idProveedor = $_POST['id'];
            $nombre    = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $cp        = $_POST['cp'];
            $telefono  = $_POST['telefono'];
            $email     = $_POST['correo'];

            $sql_update = mysqli_query($conexionDB,"UPDATE proveedores
                                                        SET Nombre='$nombre', Direccion='$direccion', Codigo_Postal='$cp', Telefono='$telefono', Email='$email'
                                                        WHERE IdProveedor=$idProveedor ");

            if($sql_update){
                $alert = '<p class="msg_save">Proveedor actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el proveedor.</p>';
            }
        }
    }

    //Mostrar Datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_proveedores.php');
        mysqli_close($conexionDB);
    }
    $idProveedor = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM proveedores WHERE IdProveedor = $idProveedor ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_proveedores.php');
    } else {

        while ($data = mysqli_fetch_array($sql)) {

            $idInstructor = $data['IdProveedor'];
            $nombre = $data['Nombre'];
            $direccion = $data['Direccion'];
            $cp = $data['Codigo_Postal'];
            $telefono = $data['Telefono'];
            $correo = $data['Email'];

        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Titanium Fit| Sistema</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Actualizar Proveedor</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idProveedor; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo" value="<?php echo $nombre; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección" value="<?php echo $direccion; ?>">
                <label for="cp">Código Postal</label>
                <input type="number" name="cp" id="cp" placeholder="Ingrese el Código postal" value="<?php echo $cp; ?>">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese un Teléfono" value="<?php echo $telefono; ?>">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico" value="<?php echo $correo; ?>"><br>
                <button type="submit" class="btn_save_1"><i class="far fa-edit"></i> Actualizar proveedor</button>
                <a href="lista_proveedores.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>