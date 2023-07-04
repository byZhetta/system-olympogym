<?php
    session_start();
    include "../conexion.php";
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
        <?php
            $busqueda = '';
            $search_proveedor = '';
            if(empty($_REQUEST['busqueda']) && empty($_REQUEST['proveedor'])){
                header("location: lista_articulos.php");
            }
            if(!empty($_REQUEST['busqueda'])){
                $busqueda = strtolower($_REQUEST['busqueda']);
                $where = "(a.Descripcion LIKE '%$busqueda%' OR a.Marca LIKE '%$busqueda%')";
                $buscar = 'busqueda='.$busqueda;
            }
            if(!empty($_REQUEST['proveedor'])){
                $search_proveedor = $_REQUEST['proveedor'];
                $where = "a.Cod_Proveedor LIKE $search_proveedor";
                $buscar = 'proveedor='.$search_proveedor;
            }
        ?>

		<h1>Lista de artículos</h1>
        <a href="registro_articulo.php" class="btn_new"><i class="fas fa-plus"></i> Crear artículo</a>

        <form action="buscar_articulo.php" mathod="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Descripción / Marca" value="<?php echo $busqueda; ?>">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
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
                    $pro = 0;
                    if(!empty($_REQUEST['proveedor'])){
                        $pro = $_REQUEST['proveedor'];
                    }
                    $query_proveedor = mysqli_query($conexionDB,"SELECT IdProveedor, Nombre FROM proveedores ORDER BY Nombre ASC");
                    $result_proveedor = mysqli_num_rows($query_proveedor);
                ?>
                <select name="proveedor" id="search_proveedor">
                    <option value="" selected>Proveedor</option>
                <?php
                    if($result_proveedor > 0){
                        while ($proveedor = mysqli_fetch_array($query_proveedor)){
                            if($pro == $proveedor["IdProveedor"]) {
                ?>
                    <option value="<?php echo $proveedor['IdProveedor']; ?>" selected><?php echo $proveedor['Nombre']; ?></option>
                <?php
                            } else {
                ?>
                    <option value="<?php echo $proveedor['IdProveedor']; ?>"><?php echo $proveedor['Nombre']; ?></option>         
                <?php 
                            }
                        }
                    }
                ?>
                </select>
                </th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM articulos AS a
                                                                        WHERE $where ");
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
                                                        WHERE $where
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
                            <td><?php echo $data["Precio_Unitario"]; ?></td>
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
<?php   if($total_paginas != 0){   ?>

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

<?php   }    ?>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>