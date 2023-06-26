<?php 
    session_start();
    include "../conexion.php";
    
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['direccion']) || empty($_POST['telefono']) ||
           empty($_POST['correo']) || empty($_POST['usuario'])){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idEmpleado = $_POST['id'];
            $nombre = $_POST['nombre'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $email = $_POST['correo'];
            $user = $_POST['usuario'];
            $rol = $_POST['rol'];


            $sql_update = mysqli_query($conexionDB,"UPDATE empleados
                                                        SET Nombre='$nombre', Direccion='$direccion', Telefono='$telefono', Email='$email', Usuario='$user', Rol='$rol'
                                                        WHERE IdEmpleado=$idEmpleado ");

            if($sql_update){
                $alert = '<p class="msg_save">Usuario actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el usuario.</p>';
            }
        }
    }

    //Mostrar Datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_usuarios.php');
        mysqli_close($conexionDB);
    }
    $iduser = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT u.IdEmpleado,u.Nombre,u.Direccion,u.Telefono,u.Email,u.Usuario,(u.Rol) as IdRol, (r.rol) as rol
                                        FROM empleados u INNER JOIN rol r on u.Rol = r.IdRol WHERE IdEmpleado = $iduser ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_usuarios.php');
    } else {
        $option = '';
        while ($data = mysqli_fetch_array($sql)) {

            $iduser = $data['IdEmpleado'];
            $nombre = $data['Nombre'];
            $direccion = $data['Direccion'];
            $telefono = $data['Telefono'];
            $correo = $data['Email'];
            $usuario = $data['Usuario'];
            $idrol = $data['IdRol'];
            $rol = $data['rol'];

            if($idrol == 1){
                $option = '<option value="'.$idrol.'" selected>'.$rol.'</option>';
            } else if($idrol == 2){
                $option = '<option value="'.$idrol.'" selected>'.$rol.'</option>';
            }

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
            <h1>Actualizar usuario</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $iduser; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo" value="<?php echo $nombre; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Ingrese una Dirección" value="<?php echo $direccion; ?>">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ingrese un Teléfono" value="<?php echo $telefono; ?>">
                <label for="correo">Email</label>
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico" value="<?php echo $correo; ?>">
                <?php if($iduser = 1){ ?>
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Ingrese un Usuario" value="<?php echo $usuario; ?>">                    
                <?php } ?>
            <?php if($iduser != 1){ ?>
                <label for="rol">Tipo Usuario</label>

                <?php
                    include "../conexion.php";
                    $query_rol = mysqli_query($conexionDB,"SELECT * FROM rol");
                    mysqli_close($conexionDB);
                    $result_rol = mysqli_num_rows($query_rol)
                ?>

                <select name="rol" id="rol" class="notItemOne">
                    <?php
                         echo $option;
                        if($result_rol > 0){
                            while ($rol = mysqli_fetch_array($query_rol)){
                    ?>         
                                <option value="<?php echo $rol["IdRol"]; ?>"><?php echo $rol["rol"]; ?></option>
                    <?php            
                            }
                        }
                    ?>
                </select>
            <?php } ?>
                <br>
                <button type="submit" class="btn_save_1"><i class="far fa-edit"></i> Actualizar usuario</button>
                <a href="lista_usuarios.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>