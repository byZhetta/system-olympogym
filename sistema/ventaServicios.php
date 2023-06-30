<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <title>Titanium Fit| Venta de Servicios</title>
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
        <div class="title_page">
            <h1>Venta Libre</h1>
        </div>

		<?php
			// Inserta el archivo de conexión
			require('../conexion.php');
			// Para rellenar la lista desplegable del formulario
			$listaClientes = "SELECT Nombre, Dni From socios";
			$listaClientes = $conexionDB->query($listaClientes);
			$grabadoConExito=false;

			// Si el formulario fue enviado, procesa los datos
			if (isset($_POST['confirmar'])) {
				$alert='';
				if ($conexionDB === false) {
					die("ERROR: No fue posible conectarse con la base de datos. " . mysqli_connect_error());
				}
				$inputError = false;

				// Recibe el dni del socio
				if (empty($_POST['dnideSocio'])) {	

					$alert='<p class="msg_error">ERROR: Por favor ingrese el DNI del Socio</p></br>';       
					$inputError = true;

				} else {
					$dniSocio = $conexionDB->escape_string($_POST['dnideSocio']);

					// Filtra los dni no registrados
					$sql= "SELECT * FROM socios WHERE Dni = '$dniSocio'";
					$resultado = $conexionDB->query($sql);
					if ( $resultado->num_rows < 1 ) {
						$alert='<p class="msg_error">
						ERROR: El DNI ingresado no corresponde a un socio registrado
						</p></br>'; 
						$inputError = true;
					}
				}
				// Recibe el monto dado
				if ($inputError != true && empty($_POST['periodo'])){
					$alert='<p class="msg_error">
					ERROR: Por favor ingrese un valor válido
					</p></br>'; 
					$inputError = true;
				} else {
					$monto = $conexionDB->escape_string($_POST['monto']);
				}

				// añade valores a la base de datos utilizando la consulta INSERT
				if ($inputError != true) {

					// Obtener la fecha actual
					$fecha=date("Y/m/j");

					// Consultar id de socio
					$consultaCodSocio="SELECT Id_Socio FROM socios
										WHERE Dni = '$dniSocio'";
					$codSocio=( ( $conexionDB->query($consultaCodSocio) )->fetch_object() )->Id_Socio;

					// Consultar id de clase
					$consultaCodClase="SELECT IdClase FROM clases
										WHERE NombreC = 'Diario'";
					$codClase=( ( $conexionDB->query($consultaCodClase) )->fetch_object() )->IdClase;

					// Consultar costo de clase
					$consultaCosto="SELECT Costo_Clase FROM clases
									WHERE NombreC = 'Diario'";
					$costo=( ( $conexionDB->query($consultaCosto) )->fetch_object() )->Costo_Clase;

					//id usuario
					$usuario = $_SESSION['idUser'];

					//sumar total en caja
					$saldcaja = mysqli_query($conexionDB,"SELECT SUM(Total_caja) + '$monto' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja)");
					$data = mysqli_fetch_array($saldcaja);
                    $totalcaja = $data['total'];

					//inserta datos de venta en caja
					$insercaja=mysqli_query($conexionDB,"INSERT INTO caja (Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado) 
					                                                VALUES ('Venta de Libre', '$monto', '$totalcaja', '$usuario', 'Abierto')");

					// Consutar id de caja
					$consultaCodCaja="SELECT MAX(IdCaja) as IdCaja FROM caja WHERE Actividad = 'Venta de Servicio'";
					$codCaja=( ( $conexionDB->query($consultaCodCaja) )->fetch_object() )->IdCaja;


					// INICIA EL BLOQUE DE TRANSACCIÓN
					try {					    
						$conexionDB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

						$instruccSQL="INSERT INTO ventas (Cod_Caja, Cod_Socio, Total) 
										VALUES ('$codCaja', '$codSocio', '$monto')";
						// Ejecutar la operación
						$queryVentas=$conexionDB->query($instruccSQL);
						if (!$queryVentas)
							throw new Exception($conexionDB->error);
						else {
							$instruccSQL="INSERT INTO detalle_venta_servicios (Cod_Venta, Cod_Clase, Periodo, Fecha_Alta, Fecha_Vencim, Total)
											VALUES (".$conexionDB->insert_id.", '11', 'Venta libre', '$fecha', '$fecha', '$monto')";
							// Ejecutar la operación
							$queryVentas_Item=$conexionDB->query($instruccSQL);
							if (!$queryVentas_Item)
								throw new Exception($conexionDB->error);
							else{
								$conexionDB->commit();

								$alert='<p class="msg_aviso_ok">
								Venta de servicio realizada !!
								</br>
								El total a abonar es de S/.'.$monto.'</p></br>';

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
				// cierra conexión
				$conexionDB->close();
			}
		?>

        <div class="datos_venta">
			<h4>Venta Libre</h4>
			<form action="ventaServicios.php" method="post" class="datos">
				<div class="wd100">
					<label>Vendedor</label>
					<p><?php echo $_SESSION['nombre']; ?></p>
				</div>
				<div class="wd40">
                	<label for="socio">DNI del Cliente</label>
					<select id="socio" name="dnideSocio">
						<option value="" selected="selected">-selecciona-</option>
						<?php
							if ($listaClientes->num_rows > 0) {
								while ($fila = $listaClientes->fetch_assoc()) {
									echo '<option value="'.$fila["Dni"].'">'.$fila["Dni"]." - ".$fila["Nombre"]."</option>";
								}
							}
						?>
					</select>
					<br>
				</div>
				<div class="wd40">
					<label for="monto">Monto</label>
					<input type="number" name="monto" placeholder="Ingrese monto" value="10" autofocus/>
				</div>
				<div class="wd30">
					<button type="submit" class="link_addone" name="confirmar"><i class="far fa-check-circle"></i> Confirmar</button>
				</div>
				<div class="wd30">                 
					<button type="submit" class="link_deleteone" name="cancelar"><i class="fas fa-ban"></i> Cancelar</button>
				</div>
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