<?php 

    include "../conexion.php"; 
    session_start();
    
    if(!empty($_POST)){
        $alert='';
        if( empty($_POST['sueldo']) ) {
            $alert='<p class="msg_error">El campo sueldo es obligatorio.</p>';
        } else {
              
            $sueldo = $_POST['sueldo'];
            $usuario = $_SESSION['idUser'];

            $consulta = mysqli_query($conexionDB,"SELECT * FROM caja");
            $resulconsulta = mysqli_num_rows($consulta);
            
            if($resulconsulta > 0){
                $sumcaja = mysqli_query($conexionDB,"SELECT SUM(Total_caja) + '$sueldo' as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
                $result = mysqli_num_rows($sumcaja);

                if($result > 0){
                    $data = mysqli_fetch_array($sumcaja);
                    $totalcaja = $data['total'];

                    $query_insert = mysqli_query($conexionDB,"INSERT INTO caja(Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado)
                                                                        VALUES('Ingreso de dinero','$sueldo','$totalcaja','$usuario','Abierto')");
                    mysqli_close($conexionDB);
                    if($query_insert){
                        header('location: index.php');
                    } else {
                        $alert = '<p class="msg_error">Error al abrir la caja.</p>';
                    }
                } else {
                    $alert = '<p class="msg_error">No se encontro ningun resultado.</p>';
                }
            } else {
                $query_insert = mysqli_query($conexionDB,"INSERT INTO caja(Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado)
                                                                    VALUES('Ingreso de dinero','$sueldo','$sueldo','$usuario','Abierto')");
                mysqli_close($conexionDB);
                header('location: index.php');
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
            <h1>Ingreso de dinero ($)</h1>
            <hr>
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="post">
                <label for="sueldo">Importe: </label>
                <input type="number" min="0.00" name="sueldo" id="sueldo" placeholder="Ingrese el importe ($)"> 
                <br>
                <button type="submit" class="link_edit"><i class="fas fa-cash-register"></i> Abrir Caja</button>
                <a href="lista_caja.php" class="link_delete" style="float: right;"><i class="fas fa-minus-circle"></i> Cancelar</a>
            </form>

        </div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>