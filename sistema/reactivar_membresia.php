<?php 
    session_start();
    include "../conexion.php";
    include 'includes/zona_horaria.php';
    // date_default_timezone_set('America/Lima');

    $listaClases = "SELECT NombreC FROM clases";
    $listaClases = $conexionDB->query($listaClases);

    if(!empty($_POST)){
        $alert = '';
        if(empty($_POST['memb_nueva'])){
            $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
        }else{
            $idSocio = $_POST['id'];
            $memb_nueva = $_POST['memb_nueva'];
            $monto = $_POST['monto'];
            $fecha_ingreso = date("Y-m-d");

            //consultar id de clase
            $consultaCodClase = "SELECT IdClase FROM clases
                                WHERE NombreC = '$memb_nueva'";
            $codClase=(($conexionDB->query($consultaCodClase))->fetch_object())->IdClase;

            //consultar costo de la clase
            $consultaCosto = "SELECT Costo_Clase FROM clases WHERE NombreC = '$memb_nueva'";
            $costo = (($conexionDB->query($consultaCosto))->fetch_object())->Costo_Clase;

            switch ($memb_nueva){
                case 'Diario':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 day'));
                    break;
                case 'Semanal':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 week'));
                    break;
                case 'Mensual':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('1 month'));
                    break;
                case 'Trimestral':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('3 month'));
                    break;
                case 'Semestral':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('6 month'));
                    break;
                case 'Anual':
                        $total = $monto; 
                        $fecha_vencimiento=date ("Y/m/j", strtotime('12 month'));
                    break;
            }

            //Actualizar cliente
            $sql_update = mysqli_query($conexionDB,"UPDATE socios SET fecha_ingreso = '$fecha_ingreso', 
                                                    fecha_vencimiento = '$fecha_vencimiento', Id_Clase = '$codClase'
                                                    WHERE Id_Socio='$idSocio'");

            //sumar total en caja
            $saldcaja = mysqli_query($conexionDB,"SELECT SUM(Total_caja) + '$total' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja)");
            $data = mysqli_fetch_array($saldcaja);
            $totalcaja = $data['total'];

            //id usuario
            $usuario = $_SESSION['idUser'];

            //inserta datos de venta en caja
            $insercaja=mysqli_query($conexionDB,"INSERT INTO caja (Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado) 
                                                            VALUES ('Venta de Servicio', '$total', '$totalcaja', '$usuario', 'Abierto')");

            //consultar id de caja
            $consultaCodCaja = "SELECT MAX(IdCaja) as IdCaja FROM caja WHERE Actividad = 'Venta Libre'";
            $codCaja = (($conexionDB->query($consultaCodCaja))->fetch_object() )->IdCaja;

            //inicia el bloque de transacion
            try{
                $conexionDB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

                $instruccSQL="INSERT INTO ventas (Cod_Caja, Cod_Socio, Total) 
                                VALUES ('$codCaja', '$idSocio', '$total')";
                $queryVentas = $conexionDB->query($instruccSQL);
                if(!$queryVentas){
                    throw new Exception($conexionDB->error);
                }else{
                    $instruccSQL="INSERT INTO detalle_venta_servicios (Cod_Venta, Cod_Clase, Periodo, Fecha_Alta, Fecha_Vencim, Total)
                                    VALUES (".$conexionDB->insert_id.", '$codClase', '$memb_nueva', '$fecha_ingreso', '$fecha_vencimiento', '$total')";
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
                }
            }

            } catch (Exception $ex){
                $conexionDB->rollback();
                $alert='<p class="msg_error">Ocurrió un error al intentar grabar la Venta!!'. $ex->getMessage() .'</p></br>';
            }

        }

    }

    //mostrar datos
    if(empty($_REQUEST['id'])){
        header('Location: lista_socio.php');
        mysqli_close($conexionDB);
    }

    $idSocio = $_REQUEST['id'];

    $sql = mysqli_query($conexionDB,"SELECT * FROM socios s 
                                    INNER JOIN clases c ON s.Id_Clase = c.IdClase
                                    WHERE Id_Socio = $idSocio");
    mysqli_close($conexionDB);
    $result_sql = mysqli_num_rows($sql);

    if($result_sql == 0){
        header('Location: lista_socio.php');
    }else {
        while ($data = mysqli_fetch_array($sql)){
            $idSocio = $data['Id_Socio'];
            $nombre = $data['Nombre'];
            $dniSocio = $data['Dni'];
            $memb_actual = $data['NombreC'];
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/scripts.php"; ?>
    <title><?php echo $nombreGym ?> | Sistema</title>
</head>
<body>
    <?php include "includes/header.php" ?>
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
            <h1>Reactivar Membresía</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $idSocio; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" readonly>
                <label for="dni">Dni</label>
                <input type="number" name="dni" id="dni" value="<?php echo $dniSocio; ?>" readonly>
                <label for="memb_actual">Membresía Actual</label>
                <input type="text" name="memb_actual" id="memb_actual" value="<?php echo $memb_actual; ?>" readonly>
                <div class="">
                	<label for="membresia">Membresía nueva</label>
					<select id="memb_nueva" name="memb_nueva">
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
                <label for="monto">Monto a pagar</label>
                <input type="number" name="monto" id="monto" value="0">
                <br>
                <button type="submit" class="btn_save_1"><i class="far fa-edit"></i> Reactivar membresía</button>
                <a href="lista_socio.php" class="link_delete_1" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>
        </div>
        <?php 
            }
        ?>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>