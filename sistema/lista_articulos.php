<?php
    include "../conexion.php";
    session_start();
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

		<h1>ARTÍCULOS</h1>
        <a href="registro_articulo.php" class="btn_new"><i class="fas fa-plus"></i> Crear artículo</a>

        <form action="buscar_articulo.php" mathod="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Descripción / Marca">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <form action="imprimir_articulo.php" method="get" class="form_search" target="_blank">
            <input type="submit" name="nPdf" id="idPdf" value="Exportar a PDF">
            <input type="submit" name="nExcel" id="idExcel" value="Exportar a Excel">       
        </form>
        <table>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>
                <?php
                    $query_proveedor = mysqli_query($conexionDB,"SELECT * FROM proveedores ORDER BY Nombre ASC");
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                ?>
                <select name="proveedor" id="search_proveedor">
                    <option value="" class="option1" selected>Proveedor</option>
                <?php
                    if($result_proveedor > 0){
                        while ($proveedor = mysqli_fetch_array($query_proveedor)){
                ?>
                    <option value="<?php echo $proveedor['IdProveedor']; ?>"><?php echo $proveedor['Nombre']; ?></option>
                <?php
                        }
                    }
                ?>
                </select>
                </th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM articulos");
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
                
                $query = mysqli_query($conexionDB,"SELECT a.IdArticulo, a.Descripcion, a.Cantidad, a.Precio_Unitario, a.Marca, p.Nombre 
                                                        FROM articulos a INNER JOIN proveedores p ON a.Cod_Proveedor = p.IdProveedor 
                                                        ORDER BY a.IdArticulo DESC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr>
                            <td><?php echo $data["IdArticulo"]; ?></td>
                            <td><?php echo $data["Descripcion"]; ?></td>
                            <td><?php echo $data["Marca"]; ?></td>
                            <td><?php echo $data["Cantidad"]; ?></td>
                            <td>S/.<?php echo $data["Precio_Unitario"]; ?></td>
                            <td><?php echo $data["Nombre"]; ?></td>
                            <td>
                                <a class="link_edit" href="editar_articulo.php?id=<?php echo $data["IdArticulo"]; ?>"><i class="far fa-edit"></i> Editar</a>
                                |
                                <a class="link_delete" href="eliminar_articulo.php?id=<?php echo $data["IdArticulo"]; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
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
            <?php } ?>
            </ul>
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>