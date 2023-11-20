<?php 
    session_start();
    include "../conexion.php";
    
    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['telefono'] ) || empty($_POST['fecha_ingreso'] ) || 
        empty($_POST['fecha_vencimiento'] || empty($_POST['imagen'])) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $idSocio   = $_POST['id'];
            $nombre    = $_POST['nombre'];
            $telefono  = $_POST['telefono'];
            $fecha_ingreso = $_POST['fecha_ingreso'];
            $fecha_vencimiento = $_POST['fecha_vencimiento'];
            $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));

            $sql_update = mysqli_query($conexionDB,"UPDATE socios
                                                        SET Nombre='$nombre', Telefono='$telefono', 
                                                        fecha_ingreso='$fecha_ingreso', fecha_vencimiento='$fecha_vencimiento',
                                                        Imagen='$imagen'
                                                        WHERE Id_Socio=$idSocio");

            if($sql_update){
                $alert = '<p class="msg_save">Socio actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el socio.</p>';
            }
        }
    }

    //Mostrar Datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_socio.php');
        mysqli_close($conexionDB);
    }
    $idsocio = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM socios WHERE Id_Socio = $idsocio ");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_socio.php');
    } else {

        while ($data = mysqli_fetch_array($sql)) {

            $idsocio = $data['Id_Socio'];
            $nombre = $data['Nombre'];
            $dni = $data['Dni'];
            $telefono = $data['Telefono'];
            $fecha_ingreso = $data['fecha_ingreso'];
            $fecha_vencimiento = $data['fecha_vencimiento'];
            $imagenV = $data['Imagen'];            
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

        <div class="form_register">
            <h1>Actualizar Socio</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $idsocio; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo" value="<?php echo $nombre; ?>">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ingrese un Teléfono" value="<?php echo $telefono; ?>">
                <label for="fecha_ingreso">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" placeholder="Ingrese fecha de ingreso" value="<?php echo $fecha_ingreso; ?>">
                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" placeholder="Ingrese fecha de vencimiento" value="<?php echo $fecha_vencimiento; ?>">
                <label for="imagen">Selecciona una imagen</label>
                <img height="40px" src="data:image/jpg;base64, <?php echo base64_encode($imagenV)?>" alt="">
                <input type="file" name="imagen" id="imagen" accept="image/*">
                <br>
                <button type="submit" class="btn_save_1"><i class="far fa-edit"></i> Actualizar socio</button>
                <a href="lista_socio.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>