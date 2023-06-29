<?php

    session_start();
    include "../conexion.php";

    $busqueda = '';
    $fecha_de = '';
    $fecha_a = '';

    if( isset($_REQUEST['busqueda']) && $_REQUEST['busqueda'] ==''){
        header("location: ventas.php");
    }

    if( isset($_REQUEST['fecha_de']) || isset($_REQUEST['fecha_a'])){
        if( $_REQUEST['fecha_de'] == '' || $_REQUEST['fecha_a'] == ''){
            header("location: ventas.php");
        }
    }

    if(!empty($_REQUEST['busqueda'])){
        if(!is_numeric($_REQUEST['busqueda'])){
            header("location: ventas.php");
        }
        $busqueda = strtolower($_REQUEST['busqueda']);
        $where = "IdVenta = $busqueda";
        $buscar = "busqueda = $busqueda";
    }

    if(!empty($_REQUEST['fecha_de']) && !empty($_REQUEST['fecha_a'])){
        $fecha_de = $_REQUEST['fecha_de'];
        $fecha_a = $_REQUEST['fecha_a'];

        $buscar = '';

        if($fecha_de > $fecha_a){
            header("location: ventas.php");
        } else if ($fecha_de == $fecha_a){
            $where = "Fecha LIKE '$fecha_de%'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        } else {
            $f_de = $fecha_de;
            $f_a = $fecha_a;
            $where = "Fecha BETWEEN '$f_de' AND '$f_a'";
            $buscar = "fecha_de=$fecha_de&fecha_a=$fecha_a";
        }
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include "includes/scripts.php"; ?>
	<title>Titanium Fit| Sistema</title>
</head>
<body>
    
    <?php include "includes/header.php"; ?>
	<section id="container">

		<h1>Ventas</h1>
        <a href="ventaArticulos.php" class="btn_new"><i class="fas fa-plus"></i> Venta Articulo</a>
        <a href="ventaServicios.php" class="btn_new"><i class="fas fa-plus"></i> Venta Servicio</a>

        <form action="buscar_venta.php" mathod="get" class="form_search">
            <input type="number" name="busqueda" id="busqueda" placeholder="Nro. Factura" value="<?php echo $busqueda; ?>">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_venta.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php echo $fecha_de; ?>" required>
                <label> A </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php echo $fecha_a; ?>" required>
                <button type="submit" class="btn_view"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <table>
            <tr>
                <th>Nro.</th>
                <th>Código caja</th>
                <th>Fecha / Hora: Venta</th>
                <th>Dni cliente</th>
                <th>Nombre cliente</th>
                <th>ID-Vendedor</th>
                <th>Total</th>
                <th>Detalle</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM ventas WHERE $where");
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
                
                $query = mysqli_query($conexionDB,"SELECT v.IdVenta, v.Fecha, v.Cod_Caja, v.Cod_Socio, v.Total, 
                                                s.Nombre as cliente, s.Dni as dnis, c.Cod_Empleado as empl, e.Nombre as nempl
                                                FROM ventas v INNER JOIN socios s ON v.Cod_Socio = s.Id_Socio 
                                                INNER JOIN caja c ON v.Cod_Caja = c.IdCaja
                                                INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado WHERE $where
                                                ORDER BY IdVenta DESC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr id="row_<?php echo $data["IdVenta"]; ?>">
                            <td><?php echo $data["IdVenta"]; ?></td>
                            <td><?php echo $data["Cod_Caja"]; ?></td>
                            <td><?php echo date("d-m-Y H:i:s", strtotime($data["Fecha"])); ?></td>
                            <td><?php echo $data["dnis"]; ?></td>
                            <td><?php echo $data["cliente"]; ?></td>
                            <td><?php echo $data["empl"]; ?>-<?php echo $data["nempl"]; ?></td>
                            <td><span>$ </span><?php echo $data["Total"]; ?></td>
                            <td>
                                <div class="div_acciones">
                                    <div>
                                        <button class="btn_view view_factura" type="button" cl="<?php echo $data["Cod_Socio"]; ?>" f="<?php echo $data["IdVenta"]; ?>"><i class="far fa-file-alt"></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
            <?php
                    }
                }
            ?>
            
        </table>
        <div class="paginador">
            <ul>
            <?php
                if($pagina != 1){
            ?>
                <li><a href="?pagina=<?php echo 1; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina-1; ?>&<?php echo $buscar; ?>"><i class="fas fa-fast-backward"></i></a></li>
            <?php
                }
                for($i=1; $i <= $total_paginas; $i++){

                    if($i == $pagina){
                        echo '<li class="pageSelected">'.$i.'</li>';
                    } else {
                        echo '<li><a href="?pagina='.$i.'&'.$buscar.'">'.$i.'</a></li>';
                    }
                }
                if($pagina != $total_paginas){
            ?>
                <li><a href="?pagina=<?php echo $pagina+1; ?>&<?php echo $buscar; ?>"><i class="fas fa-fast-forward"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas; ?>&<?php echo $buscar; ?>"><i class="fas fa-step-forward"></i></a></li>
            <?php } ?>
            </ul>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>