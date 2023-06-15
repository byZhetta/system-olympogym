<?php 

    include "../conexion.php";
    //Busqueda de artículo en venta
    $buscador=mysqli_query($conexionDB,"SELECT * FROM articulos WHERE Descripcion LIKE LOWER('%".$_POST["buscar"]."%') OR
                                                                        Marca LIKE LOWER('%".$_POST["buscar"]."%') "); 
    mysqli_close($conexionDB);
    $numero = mysqli_num_rows($buscador); ?>


        <h5 class="card-tittle">Resultados encontrados (<?php echo $numero; ?>):</h5>

<?php while($resultado = mysqli_fetch_assoc($buscador)){ ?>


        <p><?php echo $resultado["IdArticulo"]; ?> - <?php echo $resultado["Descripcion"] ?> - 
        <?php echo $resultado["Marca"] ?> - S/.<?php echo $resultado["Precio_Unitario"] ?></p>


<?php } ?>