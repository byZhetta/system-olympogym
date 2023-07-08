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
    <hr>
		<h1>CLIENTES</h1>
        <a href="registro_socio.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear Cliente</a>

        <form action="buscar_socio.php" mathod="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nombre / DNI">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <form action="imprimir_socio.php" method="get" class="form_search" target="_blank">
            <input type="submit" name="nPdf" id="idPdf" value="Exportar a PDF">
            <input type="submit" name="nExcel" id="idExcel" value="Exportar a Excel">       
        </form>
        <table>
            <tr>
                <th>DNI</th>
                <th>Nombre y Apellidos</th>
                <th>Telefono</th>
                <th>Membresia</th>
                <th>F. Inicio</th>
                <th>F. Final</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM socios");
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
                
                $query = mysqli_query($conexionDB,"SELECT * FROM socios s   
                                                    INNER JOIN clases c ON s.Id_Clase = c.IdClase
                                                    ORDER BY s.Id_Socio DESC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr>
                            <td><?php echo $data["Dni"]; ?></td>    
                            <td><?php echo $data["Nombre"]; ?></td>
                            <td><?php echo $data["Telefono"]; ?></td>
                            <td><?php echo $data["NombreC"]; ?></td>
                            <td><?php echo $data["fecha_ingreso"]; ?></td>
                            <td><?php echo $data["fecha_vencimiento"]; ?></td>
                            <td>    
                                <?php 
                                    if(date('Y-m-d') > $data["fecha_vencimiento"]){
                                ?>      
                                <a class="inactivo" href="reactivar_membresia.php?id=<?php echo $data["Id_Socio"]; ?>">Vencido</a>
                                <?php
                                    }else{
                                ?>
                                    <p class="activo">Activo</p>
                                <?php    
                                    }
                                ?>
                            </td>
                            <td>
                                <a class="link_edit" href="editar_socio.php?id=<?php echo $data["Id_Socio"]; ?>"><i class="far fa-edit"></i> Editar</a>
                                |
                                <a class="link_delete" href="eliminar_socio.php?id=<?php echo $data["Id_Socio"]; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
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