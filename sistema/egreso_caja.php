<?php 
    session_start();
    include "../conexion.php"; 
    
    if(!empty($_POST)){
        $alert='';
        if( empty($_POST['sueldo']) ){
            $alert='<p class="msg_error">El campo sueldo es obligatorio.</p>';
        } else {
            
            $sueldo = $_POST['sueldo'];
            $usuario = $_SESSION['idUser']; 
             

            $query1 = mysqli_query($conexionDB,"SELECT SUM(Total_caja) AS TOTAL FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
            $dato = mysqli_fetch_array($query1);
            $resultado = $dato['TOTAL'];

            if($resultado < $sueldo ){
                $alert = '<p class="msg_error">No hay saldo solicitado en caja.</p>';
                
            } else {
                
                $query = mysqli_query($conexionDB,"SELECT SUM(Total_caja) - '$sueldo' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
                $result = mysqli_num_rows($query);

                if($result > 0){
                    $data = mysqli_fetch_array($query);
                    $totalcaja = $data['total'];

                    $query_insert = mysqli_query($conexionDB,"INSERT INTO caja(Actividad,Monto_inicial,Monto_salida,Total_caja,Cod_Empleado,Estado)
                                                                    VALUES('Egreso de dinero','0.00','$sueldo','$totalcaja','$usuario','Cerrado')");
                                                                
                    $queryEstado = mysqli_query($conexionDB,"UPDATE caja SET Estado = 'Cerrado' WHERE Cod_Empleado = '$usuario'");

                    mysqli_close($conexionDB);
                    if($query_insert){
                        header('location: lista_caja.php');
                    } else {
                        $alert = '<p class="msg_error">Error al cerrar la caja.</p>';
                    }
                } else {
                    $alert = '<p class="msg_error">No se encontro resultado en caja.</p>';
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
	<title>Olympo gym | Sistema</title>
</head>
<body>
    <?php include "includes/header.php"; ?>
	<section id="container">

        <div class="form_register">
            <h1 style="color: red";>Egreso de dinero (S/.)</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="sueldo">Importe: </label>
                <input type="number" min="0.00" name="sueldo" id="sueldo" placeholder="Ingrese el importe (S/.)"> 
                <br>
                <button type="submit" class="link_edit"><i class="fas fa-cash-register"></i> Cerrar Caja</button>
                <a href="lista_caja.php" class="link_delete" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>