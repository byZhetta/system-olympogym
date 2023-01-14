<?php 

    include "../conexion.php";    
    session_start();

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['direccion']) ||
           empty($_POST['telefono']) || empty($_POST['correo']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $dni = $_POST['dni'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $email = $_POST['correo'];

            $query = mysqli_query($conexionDB,"SELECT * FROM socios WHERE Dni = '$dni' OR Email = '$email' ");
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = '<p class="msg_error">El DNI o el correo ya existe.</p>';
            } else {
                $query_insert = mysqli_query($conexionDB,"INSERT INTO socios(Nombre,Dni,Direccion,Telefono,Email)
                                                        VALUES('$nombre','$dni','$direccion','$telefono','$email')");
                
                if($query_insert){
                    $alert = '<p class="msg_save">Socio guardado correctamente.</p>';
                } else {
                    $alert = '<p class="msg_error">Error al guardar el socio.</p>';
                }
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
	<title>Olympo gym | Registro Socio</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1>Registro de socio</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo">
                <label for="dni">Dni</label>
                <input type="number" name="dni" id="dni" placeholder="Ingrese el DNI">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese un Teléfono">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico"><br> 
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Guardar Socio</button>
                <a href="lista_socio.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>