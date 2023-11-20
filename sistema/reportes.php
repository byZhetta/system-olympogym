<?php
    include "../conexion.php";
	include 'includes/zona_horaria.php';
    session_start();

    $fecha_de = '';
	$fecha_a = '';

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

		<h1>REPORTES</h1>   
        <div>
            <h2>Buscar por Fecha</h2>
			<form action="imprimir_ventas_por_fecha.php" method="get" class="form_search_date" target="_blank">
				<label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php $fecha_de; ?>" required>
                <label> a </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php $fecha_a; ?>" required>	 
				<label></label>
				<input type="submit" name="nPdf1" id="idPdf" value="Exportar PDF">
				<label></label>
				<input type="submit" name="nExcel1" id="idExcel" value="Exportar Excel"> 
        	</form>	
        </div>
	</section>
	
	<?php include "includes/footer.php"; ?>
</body>
</html>

