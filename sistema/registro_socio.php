<?php 

    include "../conexion.php";    
    session_start();

    $listaClases = "SELECT NombreC FROM clases";
    $listaClases = $conexionDB->query($listaClases);

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['direccion']) ||
           empty($_POST['telefono']) || empty($_POST['correo']) || empty($_POST['membresia']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $dni = $_POST['dni'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $email = $_POST['correo'];
            $membresia = $_POST['membresia'];
            $fecha_ingreso = date("Y-m-d");

            // Consultar id de clase
			$consultaCodClase="SELECT IdClase FROM clases
                                WHERE NombreC = '$membresia'";
            $codClase=( ( $conexionDB->query($consultaCodClase) )->fetch_object() )->IdClase;

            switch ($membresia){
                case 'Diario': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 day'));
                    break;
                case 'Semanal': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 week'));
                    break;
                case 'Mensual': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 month'));
                    break;
                case 'Trimestral': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('3 month'));
                    break;
                case 'Semestral': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('6 month'));
                    break;
                case 'Anual': 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('12 month'));
                    break;
            }

            $query = mysqli_query($conexionDB,"SELECT * FROM socios WHERE Dni = '$dni' OR Email = '$email' ");
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = '<p class="msg_error">El DNI o el correo ya existe.</p>';
            } else {
                $query_insert = mysqli_query($conexionDB,"INSERT INTO socios(Nombre,Dni,Direccion,Telefono,Email,fecha_ingreso,fecha_vencimiento,Id_Clase)
                                                        VALUES('$nombre','$dni','$direccion','$telefono','$email','$fecha_ingreso','$fecha_vencimiento','$codClase')");
                
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
            <h1>Registro de cliente</h1>
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
                <input type="email" name="correo" id="correo" placeholder="Ingrese un Correo electrónico">
                <div class="">
                	<label for="membresia">Membresia</label>
					<select id="membresia" name="membresia">
						<option value="" selected="selected">-selecciona-</option>
						<?php
							if ($listaClases->num_rows > 0) {
								while ($fila = $listaClases->fetch_assoc()) {
									echo '<option value="'.$fila["NombreC"].'">'.$fila["NombreC"]."</option>";
								}
							}
						?>
					</select>
				</div><br> 
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Guardar CLiente</button>
                <a href="lista_socio.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>