<?php 
    session_start();
    include "../conexion.php";
    
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['direccion']) || empty($_POST['telefono']) ||
           empty($_POST['correo']) || empty($_POST['sueldo']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idInstructor   = $_POST['id'];
            $nombre    = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $telefono  = $_POST['telefono'];
            $email     = $_POST['correo'];
            $sueldo    = $_POST['sueldo'];


            $sql_update = mysqli_query($conexionDB,"UPDATE instructores
                                                        SET Nombre='$nombre', Direccion='$direccion', Telefono='$telefono', Email='$email', Sueldo='$sueldo'
                                                        WHERE Id_Instructor=$idInstructor ");

            if($sql_update){
                $alert = '<p class="msg_save">Instructor actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el instructor.</p>';
            }
        }
    }

    //Mostrar Datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_instructores.php');
        mysqli_close($conexionDB);
    }
    $idInstructor = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM instructores WHERE Id_Instructor = $idInstructor ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_instructores.php');
    } else {

        while ($data = mysqli_fetch_array($sql)) {

            $idInstructor = $data['Id_Instructor'];
            $nombre = $data['Nombre'];
            $dni = $data['Dni'];
            $direccion = $data['Direccion'];
            $telefono = $data['Telefono'];
            $correo = $data['Email'];
            $sueldo = $data['Sueldo'];

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
            <h1>Actualizar Instructor</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idInstructor; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo" value="<?php echo $nombre; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección" value="<?php echo $direccion; ?>">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese un Teléfono" value="<?php echo $telefono; ?>">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico" value="<?php echo $correo; ?>">
                <label for="sueldo">Sueldo</label>
                <input type="number" name="sueldo" id="sueldo" placeholder="Ingrese un Sueldo estimado" value="<?php echo $sueldo; ?>"><br>
                <button type="submit" class="btn_save_1"><i class="far fa-edit"></i> Actualizar instructor</button>
                <a href="lista_instructores.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>