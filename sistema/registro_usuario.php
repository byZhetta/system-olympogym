<?php 

    include "../conexion.php";    
    session_start();

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['direccion']) ||
           empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['usuario']) ||
           empty($_POST['clave']) || empty($_POST['rol'])){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $dni = $_POST['dni'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $email = $_POST['correo'];
            $user = $_POST['usuario'];
            $clave = md5($_POST['clave']);
            $rol = $_POST['rol'];

            $query = mysqli_query($conexionDB,"SELECT * FROM empleados WHERE Usuario = '$user' OR Email = '$email' ");
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = '<p class="msg_error">El correo o el usuario ya existe.</p>';
            } else {
                $query_insert = mysqli_query($conexionDB,"INSERT INTO empleados(Nombre,Dni,Direccion,Telefono,Email,Usuario,Clave,Rol)
                                                        VALUES('$nombre','$dni','$direccion','$telefono','$email','$user','$clave','$rol')");
                mysqli_close($conexionDB);
                if($query_insert){
                    $alert = '<p class="msg_save">Usuario creado correctamente.</p>';
                } else {
                    $alert = '<p class="msg_error">Error al crear el usuario.</p>';
                }
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Olympo gym | Registro Usuario</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
        <hr>
            <h1>Registro de usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo">
                <label for="dni">Dni</label>
                <input type="text" name="dni" id="dni" placeholder="Ingrese el DNI">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ingrese un Teléfono">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico">
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Ingrese un Usuario">
                <label for="clave">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Ingrese una Contraseña">
                <label for="rol">Tipo Usuario</label>
                
                <?php
                    $query_rol = mysqli_query($conexionDB,"SELECT * FROM rol");
                    mysqli_close($conexionDB);
                    $result_rol = mysqli_num_rows($query_rol)
                ?>

                <select name="rol" id="rol">
                    <option value="0">-Seleccione-</option>
                    <?php
                        if($result_rol > 0){
                            while ($rol = mysqli_fetch_array($query_rol)){
                    ?>         
                                <option value="<?php echo $rol["IdRol"]; ?>"><?php echo $rol["rol"]; ?></option>
                    <?php            
                            }
                        }
                    ?>
                </select><br>
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Crear usuario</button>
                <a href="lista_usuarios.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>