<?php
    include "../conexion.php";
    session_start();
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

		<h1>Ventas</h1>
        <a href="ventaArticulos.php" class="btn_new"><i class="fas fa-plus"></i> Venta Articulo</a>
        <a href="ventaServicios.php" class="btn_new"><i class="fas fa-plus"></i> Venta Servicio</a>

        <form action="buscar_venta.php" mathod="get" class="form_search">
            <input type="number" name="busqueda" id="busqueda" placeholder="Nro. Factura">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <form action="imprimir_ventas.php" method="get" class="form_search"  target="_blank">
            <input type="submit" name="nPdf" id="idPdf" value="Exportar PDF">
            <input type="submit" name="nExcel" id="idExcel" value="Exportar Excel">       
        </form>
        <div>
            <h5>Buscar por Fecha</h5>
            <form action="buscar_venta.php" method="get" class="form_search_date">
                <label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" required>
                <label> A </label>
                <input type="date" name="fecha_a" id="fecha_a" required>
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
                <th>Factura</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM ventas");
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
                                                    INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado
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
                            <td><span>S/.</span><?php echo $data["Total"]; ?></td>
                            <td>
                                <div class="div_acciones">
                                    <div>
                                        <button class="btn_view view_factura" type="button" cl="<?php echo $data["Cod_Socio"]; ?>" f="<?php echo $data["IdVenta"]; ?>"><i class="fas fa-print"></i></button>
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
            if ($total_registro) {
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
            <?php } }?>
            </ul>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>