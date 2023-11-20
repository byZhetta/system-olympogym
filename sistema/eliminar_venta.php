<?php 
    session_start();
    include "../conexion.php";

    if(!empty($_POST)){
        if(empty($_POST['IdDetalle_venta_serv'])){
            header("location: ventas.php");
            mysqli_close($conexionDB);
        }
        
        $IdDetalle_venta_serv = $_POST['IdDetalle_venta_serv'];
        
        $query_delete = mysqli_query($conexionDB,"DELETE FROM detalle_venta_servicios where IdDetalle_venta_serv = $IdDetalle_venta_serv");
        
        if(empty($_POST['IdVenta'])){
            header("location: ventas.php");
            mysqli_close($conexionDB);
        }
        $IdVenta = $_POST['IdVenta'];
        $query_delete = mysqli_query($conexionDB,"DELETE FROM ventas WHERE IdVenta = $IdVenta");
        mysqli_close($conexionDB);

        if($query_delete){
            header("location: ventas.php");
        }else{
            echo "Error al eliminar";
        }
    }
 

 

    if(empty($_REQUEST['id'])){
        header("location: ventas.php");
        mysqli_close($conexionDB);
    } else {
        $IdVenta = $_REQUEST['id'];

        $query = mysqli_query($conexionDB, "SELECT * FROM ventas v
                        INNER JOIN socios s ON v.Cod_socio = s.Id_Socio
                        INNER JOIN detalle_venta_servicios dvs ON v.IdVenta = dvs.Cod_Venta
                        INNER JOIN caja c ON v.Cod_Caja = c.IdCaja
                        WHERE IdVenta = $IdVenta");
        mysqli_close($conexionDB);
        $result = mysqli_num_rows($query);

        if($result > 0){
            while($data = mysqli_fetch_array($query)){
                $nombre = $data['Nombre'];
                $dni = $data['Dni'];
                $fecha = $data['Fecha'];
                $total = $data['Total'];
                $IdDetalle_venta_serv = $data['IdDetalle_venta_serv'];
                $actividad = $data['Actividad'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
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
            <p>Dni: <span><?php echo $dni; ?></span></p>
            <p>Fecha: <span><?php echo $fecha; ?></span></p>
            <p>Total: <span><?php echo $total; ?></span></p>
            <p>Actividad: <span><?php echo $actividad?></span></p>

            <form method="post" action="">
                <input type="hidden" name="IdVenta" value="<?php echo $IdVenta; ?>">
                <input type="hidden" name="IdDetalle_venta_serv" value="<?php echo $IdDetalle_venta_serv; ?>">
                <a href="ventas.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
                <button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
            </form>
        </div>

	</section>

	<?php include "includes/footer.php"; ?>
</body>
</html>