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

		<h1>INSTRUCTORES</h1>
        <a href="registro_instructor.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear Instructor</a>

        <form action="buscar_instructor.php" mathod="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nombre / DNI / Email">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>
        <!-------
        <form action="imprimir_instructor.php" method="get" class="form_search" target="_blank">
            <input type="submit" name="nPdf" id="idPdf" value="Exportar a PDF">
            <input type="submit" name="nExcel" id="idExcel" value="Exportar a Excel">       
        </form>-------> 
        <table>
            <tr>
                <th>Nro.</th>
                <th>Nombre</th>
                <th>Dni</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Sueldo</th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM instructores");
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
                
                $query = mysqli_query($conexionDB,"SELECT * FROM instructores
                                                    ORDER BY Id_Instructor ASC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr>
                            <td><?php echo $data["Id_Instructor"]; ?></td>
                            <td><?php echo $data["Nombre"]; ?></td>
                            <td><?php echo $data["Dni"]; ?></td>
                            <td><?php echo $data["Direccion"]; ?></td>
                            <td><?php echo $data["Telefono"]; ?></td>
                            <td><?php echo $data["Email"]; ?></td>
                            <td>S/.<?php echo $data["Sueldo"]; ?></td>
                            <td>
                                <a class="link_edit" href="editar_instructor.php?id=<?php echo $data["Id_Instructor"]; ?>"><i class="far fa-edit"></i> Editar</a>
                                |
                                <a class="link_delete" href="eliminar_instructor.php?id=<?php echo $data["Id_Instructor"]; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
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