<?php
    session_start();
    include "../conexion.php";
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

		<h1>Membresias</h1>

        <div class="form_search">
            <a href="registro_clase.php" class="btn_new"><i class="fas fa-plus"></i> Crear nueva membresía</a>
        </div>
            
    <div class="containerTable">
        <table>
            <tr>
                <th>Nro.</th>
                <th>Nombre</th>
                <th>Duración</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM clases");
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
                
                $query = mysqli_query($conexionDB,"SELECT c.IdClase, c.NombreC, c.Duracion, c.Costo_Clase 
                                                    FROM clases as c
                                                    ORDER BY c.IdClase ASC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr>
                            <td><?php echo $data["IdClase"]; ?></td>
                            <td><?php echo $data["NombreC"]; ?></td>
                            <td><?php echo $data["Duracion"]; ?></td>
                            <td>S/.<?php echo $data["Costo_Clase"]; ?></td>
                            <td>
                                <a class="link_edit" href="editar_clase.php?id=<?php echo $data["IdClase"]; ?>"><i class="far fa-edit"></i> Editar</a>
                                <?php if($_SESSION['rol'] == 1){ ?>	
                                |
                                <a class="link_delete" href="eliminar_clase.php?id=<?php echo $data["IdClase"]; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
                                <?php } ?>
                            </td> 
                        </tr>
            <?php
                    }
                }
            ?>
            
        </table>
    </div>
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