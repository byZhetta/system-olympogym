<?php
    include "../conexion.php";
    session_start();
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

		<h1>Flujo de caja</h1>
        <?php 
            $usuario = $_SESSION['idUser'];
            $testado = mysqli_query($conexionDB,"SELECT IdCaja, Estado FROM caja WHERE Estado = 'Abierto' AND Cod_Empleado = '$usuario'");
            $status = mysqli_num_rows($testado);
            if($status > 0){ ?>
                <a href="egreso_caja.php" class="link_delete"><i class="fas fa-minus-circle"></i> Egreso</a>
        <?php } else { ?>
                <a href="ingreso_caja.php" class="link_edit"><i class="fas fa-plus-circle"></i> Ingreso</a>
        <?php } ?>
        
        <form action="" mathod="" class="form_search"  style="background: #fff";>
            <h2>SALDO TOTAL S/. </h2>
            <?php 
            $totalc = mysqli_query($conexionDB,"SELECT SUM(Total_caja) as TOTALc FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
            $data = mysqli_fetch_array($totalc);
            $totalcaja = $data['TOTALc'];
            ?>
            <p><?php echo $totalcaja ?></p>
        </form>

    <div class="containerTable">
        <table>
            <tr>
                <th>Código</th>
                <th>Fecha / Hora: Movimientos</th>
                <th>Actividad</th>
                <th>Entrada (S/.)</th>
                <th>Salida (S/.)</th>
                <th>Saldo Total Act.(S/.)</th>
                <th>ID-Usuario</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM caja WHERE Estado = 'Abierto' AND Cod_Empleado = '$usuario'");
                $result_register = mysqli_fetch_array($sql_registe);
                $total_registro = $result_register['total_registro'];

                $por_pagina = 10;

                if(empty($_GET['pagina'])){
                    $pagina = 1;
                } else {
                    $pagina = $_GET['pagina'];
                }

                $desde = ($pagina-1) * $por_pagina;
                $total_paginas = ceil($total_registro / $por_pagina);
                
                $query = mysqli_query($conexionDB,"SELECT c.IdCaja, c.FechaApertura, c.Actividad, c.Monto_inicial, c.Monto_salida, c.Total_caja, c.Cod_Empleado, e.Nombre 
                                                    FROM caja c INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado
                                                    WHERE c.Estado = 'Abierto' AND c.Cod_Empleado = '$usuario'
                                                    ORDER BY c.IdCaja DESC LIMIT $desde,$por_pagina");
                
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){
                    ?>
                        <tr>
                            <td><?php echo $data["IdCaja"]; ?></td>
                            <td><?php   $fechac = $data["FechaApertura"]; 
                                        $nfecha =  date("d-m-Y H:i:s", strtotime($fechac));
                                        echo $nfecha; ?></td>
                            <td><?php echo $data["Actividad"]; ?></td>
                            <td>S/. <?php echo $data["Monto_inicial"]; ?></td>
                            <td>S/. <?php echo $data["Monto_salida"]; ?></td>
                            <td>S/. <?php echo $data["Total_caja"]; ?></td>
                            <td><?php echo $data["Cod_Empleado"]; ?>-<?php echo $data["Nombre"]; ?></td>
                        </tr>
            <?php
                    }
                } 
            ?>
        </table>
    </div>
        <div class="paginador">
            <?php if ($total_registro) { ?>
            <ul>
            <?php  
                if($pagina != 1){
            ?>
                <li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-fast-backward"></i></a></li>
            <?php
                }
                for($i=1; $i <= $total_paginas; $i++){
                    if($i == $pagina){
                        echo '<li class="pageSelected">'.$i.'</li>';
                    } else {
                        echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';
                    }
                }
                if($pagina != $total_paginas){
            ?>
                <li><a href="?pagina=<?php echo $pagina+1; ?>"><i class="fas fa-fast-forward"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas; ?>"><i class="fas fa-step-forward"></i></a></li>
            </ul>
            <?php }  }
                if ($total_registro == 0){ ?>
                <div class="text-list"><p><i>Caja cerrada: Abrir caja para realizar ventas</i></p></div>
            <?php } ?>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>