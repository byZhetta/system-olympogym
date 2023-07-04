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
         
            $busqueda = strtolower($_REQUEST['busqueda']);
            if(empty($busqueda)){
                header("location: lista_usuarios.php");
                mysqli_close($conexionDB);
            }

        ?>

		<h1>Lista de usuarios</h1>
        <a href="registro_usuario.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear Usuario</a>

        <form action="buscar_usuario.php" mathod="get" class="form_search">
            <input type="text" name="busqueda" id="busqueda" placeholder="Nombre / DNI / Rol" value="<?php echo $busqueda; ?>">
            <button type="submit" class="btn_search"><i class="fas fa-search"></i></button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Dni</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php
                //paginador
                $rol = '';
                if($busqueda == 'administrador'){
                    $rol = " OR Rol LIKE '%1%' ";
                } else if($busqueda == 'vendedor'){
                    $rol = " OR Rol LIKE '%2%' ";
                }

                $sql_registe = mysqli_query($conexionDB,"SELECT COUNT(*) AS total_registro FROM empleados
                                                            WHERE (Nombre LIKE '%$busqueda%' OR
                                                                    Dni LIKE '%$busqueda%' OR
                                                                    Email LIKE '%$busqueda%'
                                                                    $rol) ");
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
                
                $query = mysqli_query($conexionDB,"SELECT u.IdEmpleado, u.Nombre, u.Dni, u.Direccion, u.Telefono, u.Email, u.Usuario, r.Rol FROM
                                                    empleados u INNER JOIN rol r ON u.Rol = r.IdRol 
                                                    WHERE (u.Nombre LIKE '%$busqueda%' OR
                                                            u.Dni LIKE '%$busqueda%' OR
                                                            u.Email LIKE '%$busqueda%' OR
                                                            r.Rol LIKE '%$busqueda%') 
                                                    ORDER BY u.IdEmpleado ASC LIMIT $desde,$por_pagina");
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
                if($result > 0){
                    while ($data = mysqli_fetch_array($query)){

                    ?>
                        <tr>
                            <td><?php echo $data["IdEmpleado"]; ?></td>
                            <td><?php echo $data["Nombre"]; ?></td>
                            <td><?php echo $data["Dni"]; ?></td>
                            <td><?php echo $data["Direccion"]; ?></td>
                            <td><?php echo $data["Telefono"]; ?></td>
                            <td><?php echo $data["Email"]; ?></td>
                            <td><?php echo $data["Usuario"]; ?></td>
                            <td><?php echo $data["Rol"]; ?></td>
                            <td>
                                <a class="link_edit" href="editar_usuario.php?id=<?php echo $data["IdEmpleado"]; ?>"><i class="far fa-edit"></i> Editar</a>
                                <?php if($data['IdEmpleado'] != 1) { ?>
                                |
                                <a class="link_delete" href="eliminar_usuario.php?id=<?php echo $data["IdEmpleado"]; ?>"><i class="far fa-trash-alt"></i> Eliminar</a>
                                <?php
                                    }
                                ?>
                            </td> 
                        </tr>
            <?php
                    }
                }
            ?>
            
        </table>
<?php
    if($total_registro != 0){    
?>  
        <div class="paginador">
            <ul>
            <?php
                if($pagina != 1){
            ?>
                <li><a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-step-backward"></i></a></li>
                <li><a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-fast-backward"></i></a></li>
            <?php
                }
                for($i=1; $i <= $total_paginas; $i++){

                    if($i == $pagina){
                        echo '<li class="pageSelected">'.$i.'</li>';
                    } else {
                        echo '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.'">'.$i.'</a></li>';
                    }
                }
                if($pagina != $total_paginas){
            ?>
                <li><a href="?pagina=<?php echo $pagina+1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-fast-forward"></i></a></li>
                <li><a href="?pagina=<?php echo $total_paginas; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-step-forward"></i></a></li>
            <?php } ?>
            </ul>
        </div>
<?php } ?>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>