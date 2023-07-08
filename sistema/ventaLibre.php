<?php 
    include "../conexion.php";
    session_start();

    $nombreGeneral = 'General SAC';
    $socioGeneral = "SELECT Nombre FROM socios WHERE Nombre = '$nombreGeneral'";
    $clienteGeneral = ($conexionDB->query($socioGeneral) ->fetch_object() )->Nombre;

    if(!empty($_POST)){
        $alert = '';
        if(empty($_POST['nombreCliente']) || empty($_POST['monto']) ){
            $alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
        } else {
            $dniSocio = $_POST['nombreCliente'];
            $costo = $_POST['monto'];

            //id usuario 
            $usuario = $_SESSION['idUser'];

            //sumar el total en la caja
            $saldoCaja = mysqli_query($conexionDB, "SELECT SUM(Total_caja) + '$costo' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja)");
            $data = mysqli_fetch_array($saldoCaja);
            $totalCaja = $data['total'];

            //inserta datos de venta en caja
            $inserCaja = mysqli_query($conexionDB, "INSERT INTO caja (Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado)
                                                    VALUES ('Venta Libre', '$costo','$totalCaja','$usuario','Abierto')");
            
            //consultar id de caja
            $consultaCodCaja = "SELECT MAX(IdCaja) as IdCaja FROM caja WHERE Actividad = 'Venta Libre'";
            $codCaja = (($conexionDB->query($consultaCodCaja))->fetch_object() )->IdCaja;
            
            //inicia bloque de transanción
            try {
                $conexionDB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
                
                //Fecha de venta
                $fecha_venta = date('Y-m-d');

                //Consultar codigo de clase
                $nombreClase = "Diario";
                $codigoClaseSQL = "SELECT IdClase FROM clases WHERE NombreC = '$nombreClase'";
                $codClase = (($conexionDB->query($codigoClaseSQL))->fetch_object())->IdClase;
                
                //Consultar id Cliente/socio

                $codigoSocioSQL = "SELECT Id_Socio FROM socios WHERE Nombre = '$nombreGeneral'"; 
                $codigoSocio = (($conexionDB->query($codigoSocioSQL))->fetch_object())->Id_Socio;

                $ventaSql = "INSERT INTO ventas (Cod_Caja, Cod_Socio, total)
                            VALUES ('$codCaja', '$codigoSocio', '$costo')";
                
                $queryVentas = $conexionDB->query($ventaSql);
                if(!$queryVentas){
                    throw new Exception($conexionDB->error);
                }else{
                    $detalleSQL = "INSERT INTO detalle_venta_servicios (Cod_Venta, Cod_Clase, Fecha_Alta, Total)
                                    VALUES (".$conexionDB->insert_id.",'$codClase', '$fecha_venta', '$costo')";
                    //ejecutar operacion
                    $queryVentas_item = $conexionDB->query($detalleSQL);
                    if(!$queryVentas_item){
                        throw new Exception($conexionDB->error);
                    }else{
                        $conexionDB->commit();
                        
                        $alert = '<p class="msg_aviso_ok">
                        ¡Venta realizada con éxito!
                        </br>
                        El total a abonar es de S/.'.$costo.'</p></br>';
                    }
                }
        
            } catch (Exception $ex){
                $conexionDB->roollback();
                $alert = '<p class ="mgs_error">Ocurrió un error al intentar grabar la venta!'.$ex->getMessage() .'</p></br>';
            }
        }
        mysqli_close($conexionDB);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php" ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platinium Fit | Venta Libre</title>
</head>
<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <?php 
            include "../conexion.php";
            $usuario = $_SESSION['idUser'];
            $query = mysqli_query($conexionDB, "SELECT Estado, IdCaja FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
            $conexionDB->close();
            $resultado = mysqli_fetch_array($query);
            $estado = $resultado['Estado'];
            if($estado == 'Abierto'){
        ?>

        <div class="form_register">
			<h1>Venta Libre</h1>
            <br>
            <div class="Alert"><?php echo isset($alert) ? $alert : ''; ?></div>
			<form action=""method="post" class="datos" >
				<div class="wd100">
					<label>Vendedor</label>
					<p><?php echo $_SESSION['nombre']; ?></p>
				</div>
				<div class="wd40">
                	<label for="socio">Nombre del socio</label>
					<input type="text" value="<?php echo $clienteGeneral ?>" readonly name="nombreCliente">
				</div>
				<div class="wd40">
                    <label for="descuento">Monto</label>
				    <input type="number" name="monto" placeholder="Ingrese el monto" value="" autofocus/><br>
				</div>
				<div class="wd30">
					<button type="submit" class="link_addone" name="confirmar"><i class="far fa-check-circle"></i> Confirmar</button>
				</div>
				<div class="wd30">                 
					<button type="submit" class="link_deleteone" name="cancelar"><i class="fas fa-ban"></i> Cancelar</button>
				</div>
			</form>
        </div>
        <?php } else {
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