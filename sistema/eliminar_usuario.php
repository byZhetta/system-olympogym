<?php
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){
        if($_POST['idusuario'] == 1){
            header("location: lista_usuarios.php");
            mysqli_close($conexionDB);
            exit;
        }        

        $idUsuario = $_POST['idusuario'];

        $query_delete = mysqli_query($conexionDB,"DELETE FROM empleados WHERE IdEmpleado = $idUsuario ");
        mysqli_close($conexionDB);
    }
    
    
    if(empty($_REQUEST['id']) || $_REQUEST['id'] == 1){
        header("location: lista_usuarios.php");
        mysqli_close($conexionDB);
    } else {

        $idUsuario = $_REQUEST['id'];

        $query = mysqli_query($conexionDB,"SELECT u.Nombre,u.Usuario,r.rol
                                            FROM empleados u
                                            INNER JOIN rol r
                                            ON u.Rol = r.IdRol
                                            WHERE u.IdEmpleado = $idUsuario ");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while ($data = mysqli_fetch_array($query)) {
                $nombre = $data['Nombre'];
                $usuario = $data['Usuario'];
                $rol = $data['rol'];
            }
        } else {
            header("location: lista_usuarios.php");
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
    <?php include "includes/texto.php"; ?>
	<title><?php echo $nombreGym ?></title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	
	<section id="container">
		<div class="data_delete">
            <i class="fas fa-user-times fa-7x" style="color: #e66262"></i>
            <br>
            <h2>¿Está suguro de eliminar el siguiente registro?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Usuario: <span><?php echo $usuario; ?></span></p>
            <p>Tipo Usuario: <span><?php echo $rol; ?></span></p>

            <form method="post" action="">
                <input type="hidden" name="idusuario" value="<?php echo $idUsuario; ?>">
                <a href="lista_usuarios.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>