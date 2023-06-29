<?php 

    include "../conexion.php";    
    session_start();

    $listaClases = "SELECT NombreC FROM clases";
    $listaClases = $conexionDB->query($listaClases);

    if(!empty($_POST)){
        $alert='';
        if(empty($_POST['nombre']) || empty($_POST['dni']) || empty($_POST['telefono']) || 
        empty($_POST['membresia']) ){
               $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {

            $nombre = $_POST['nombre'];
            $dni = $_POST['dni'];
            $telefono = $_POST['telefono'];
            $membresia = $_POST['membresia'];
            $fecha_ingreso = date("Y-m-d");
            $descuento = $_POST['descuento'];

            // Consultar id de clase
			$consultaCodClase="SELECT IdClase FROM clases
                                WHERE NombreC = '$membresia'";
            $codClase=( ( $conexionDB->query($consultaCodClase) )->fetch_object() )->IdClase;

            // Consultar costo de clase
            $consultaCosto="SELECT Costo_Clase FROM clases
                            WHERE NombreC = '$membresia'";
            $costo=( ( $conexionDB->query($consultaCosto) )->fetch_object() )->Costo_Clase;

            switch ($membresia){
                case 'Diario':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 day'));
                    break;
                case 'Semanal':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 week'));
                    break;
                case 'Mensual':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 month'));
                    break;
                case 'Trimestral':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('3 month'));
                    break;
                case 'Semestral':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('6 month'));
                    break;
                case 'Anual':
                        $total = $costo - $descuento; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('12 month'));
                    break;
            }
            
            $query = mysqli_query($conexionDB,"SELECT * FROM socios WHERE Dni = '$dni' OR Telefono = '$telefono' ");
            $result = mysqli_fetch_array($query);

            if($result > 0){
                $alert = '<p class="msg_error">El DNI o el telefono ya existe.</p>';
                
            } else {
                $query_insert = mysqli_query($conexionDB,"INSERT INTO socios(Nombre,Dni,Telefono,fecha_ingreso,fecha_vencimiento,Id_Clase)
                                                        VALUES('$nombre','$dni','$telefono','$fecha_ingreso','$fecha_vencimiento','$codClase')");
                
            // Consultar id de socio
            $consultaCodSocio="SELECT Id_Socio FROM socios
                             WHERE Dni = '$dni'";
            $codSocio=( ( $conexionDB->query($consultaCodSocio) )->fetch_object() )->Id_Socio;

            //id usuario
            $usuario = $_SESSION['idUser'];

            //sumar total en caja
            $saldcaja = mysqli_query($conexionDB,"SELECT SUM(Total_caja) + '$total' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja)");
            $data = mysqli_fetch_array($saldcaja);
            $totalcaja = $data['total'];

            //inserta datos de venta en caja
            $insercaja=mysqli_query($conexionDB,"INSERT INTO caja (Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado) 
                                                            VALUES ('Venta de Servicio', '$total', '$totalcaja', '$usuario', 'Abierto')");

            // Consutar id de caja
            $consultaCodCaja="SELECT MAX(IdCaja) as IdCaja FROM caja WHERE Actividad = 'Venta de Servicio'";
            $codCaja=( ( $conexionDB->query($consultaCodCaja) )->fetch_object() )->IdCaja;
                if($query_insert){
                    $alert = '<p class="msg_save">Socio guardado correctamente.</p>';
                } else {
                    $alert = '<p class="msg_error">Error al guardar el socio.</p>';
                }

            // INICIA EL BLOQUE DE TRANSACCIÓN
            try {					    
                $conexionDB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

                $instruccSQL="INSERT INTO ventas (Cod_Caja, Cod_Socio, Total) 
                                VALUES ('$codCaja', '$codSocio', '$total')";
                // Ejecutar la operación
                $queryVentas=$conexionDB->query($instruccSQL);
                if (!$queryVentas)
                    throw new Exception($conexionDB->error);
                else {
                    $instruccSQL="INSERT INTO detalle_venta_servicios (Cod_Venta, Cod_Clase, Periodo, Fecha_Alta, Fecha_Vencim, Total)
                                    VALUES (".$conexionDB->insert_id.", '$codClase', '$membresia', '$fecha_ingreso', '$fecha_vencimiento', '$total')";
                    // Ejecutar la operación
                    $queryVentas_Item=$conexionDB->query($instruccSQL);
                    if (!$queryVentas_Item)
                        throw new Exception($conexionDB->error);
                    else{
                        $conexionDB->commit();

                        $alert='<p class="msg_aviso_ok">
                        Venta de servicio realizada !!
                        </br>
                        El total a abonar es de S/.'.$total.'</p></br>';

                        // Esta variable permite mostrar el botón "Imprimir"
                        $grabadoConExito=true;

                        // Obtener el núm de la factura
                        $consulta="SELECT IdVenta FROM ventas
                                    WHERE Cod_Socio = '$codSocio'
                                    AND Total = '$total' ";
                        $numFactura=( ( $conexionDB->query($consulta) )->fetch_object() )->IdVenta;
                    }
                }
            } catch (Exception $ex) {
                $conexionDB->rollback();
                $alert='<p class="msg_error">Ocurrió un error al intentar grabar la Venta!!'. $ex->getMessage() .'</p></br>';
            }
            // finaliza el bloque de transacción   
        }
        mysqli_close($conexionDB);
        
            }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Olympo gym | Registro Cliente</title>
</head>
<body>
    <?php include "includes/header.php"; ?>
	<section id="container">
        <?php
            include "../conexion.php";
			$usuario = $_SESSION['idUser']; 
            $query = mysqli_query($conexionDB,"SELECT Estado, IdCaja FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
			$conexionDB->close();
            $resultado = mysqli_fetch_array($query);
			$estado = $resultado['Estado'];
            if($estado == 'Abierto'){
        ?>
        <div class="form_register">
        <hr>
            <h1>Registro de cliente</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="nombre">Nombre y Apellidos</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese Nombre Completo">
                  <label for="dni">Dni</label>
                <input type="number" name="dni" id="dni" placeholder="Ingrese el DNI">
                <label for="telefono">Teléfono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Ingrese un Teléfono">
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
				</div>
                <div class="wd100">
                	<label for="descuento">Descuento</label>
					<input type="number" name="descuento" placeholder="Ingrese el descuento" value="0"/>
					<br>
				</div><br> 
                <button type="submit" class="btn_save_1"><i class="far fa-save"></i> Guardar Cliente</button>
                <a href="lista_socio.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>
        <?php
	        } else {
        ?>
                <div class="data_delete">
                    <i class="fas fa-cash-register fa-7x" style="color: #e66262"></i>
                    <br>
                    <h1 style="color: #ff1a1a; font-size: 25px;">DEBE ABRIR CAJA PARA INICIAR LA VENTA</h1>
                        <br><br>
                        <a href="lista_caja.php"><button type="submit" class="btn_save"><i class="fas fa-cash-register"></i> Actividad de Caja</button></a>
                </div>
        <?php
            }
        ?>                    
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>