<?php
    include "../conexion.php";
    session_start();

    $fecha_de = '';
	$fecha_a = '';
	$where = "Fecha BETWEEN '$fecha_de' AND '$fecha_a'";
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
            <h5>Buscar por Fecha</h5>
			<form action="" method="post" class="form_search_date">
				<label>De: </label>
                <input type="date" name="fecha_de" id="fecha_de" value="<?php $fecha_de; ?>" required>
                <label> a </label>
                <input type="date" name="fecha_a" id="fecha_a" value="<?php $fecha_a; ?>" required>	 
				<br> 
        	</form>	
			</form>	
			<form action="" method="get" class="form_search"  target="_blank">
            		<input type="submit" name="nPdf" id="idPdf" value="Exportar PDF">
            		<input type="submit" name="nExcel" id="idExcel" value="Exportar Excel">       
        	</form>	
        </div>
	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>

