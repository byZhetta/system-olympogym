<?php 
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
    
    <?php

	    include "includes/header.php";
		include "../conexion.php";

		$query_dash = mysqli_query($conexionDB,"CALL dataDashboard();");
		$result_dash = mysqli_num_rows($query_dash);
		if($result_dash > 0){
			$data_dash = mysqli_fetch_assoc($query_dash);
			mysqli_close($conexionDB);
		}

	?>
	<br>
	<br>
	<section id="container">
		<div class="divContainer">
		    <div>
			    <h1 class="titlePanelControl">Panel de control</h1>
			</div>
			<div class="dashboard">
			    <?php if($_SESSION['rol'] == 1){ ?>	
			    <a href="lista_usuarios.php">
				    <i class="fas fa-user"></i>
					<p>
					    <strong>Usuarios</strong><br>
						<span><?= $data_dash['usuarios']; ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_socio.php">
				    <i class="fas fa-users"></i>
					<p>
					    <strong>Socios</strong><br>
						<span><?= $data_dash['socios']; ?></span>
					</p>
				</a>
				<?php if($_SESSION['rol'] == 1){ ?>	
				<a href="lista_proveedores.php">
				    <i class="fas fa-building"></i>
					<p>
					    <strong>Proveedores</strong><br>
						<span><?= $data_dash['proveedores']; ?></span>
					</p>
				</a>
				<?php } ?>
				<a href="lista_articulos.php">
				    <i class="fas fa-cubes"></i>
					<p>
					    <strong>Artículos</strong><br>
						<span><?= $data_dash['articulos']; ?></span>
					</p>
				</a>
				<a href="ventas.php">
				    <i class="far fa-file-alt"></i>
					<p>
					    <strong>Ventas</strong><br>
						<span><?= $data_dash['ventas']; ?></span>
					</p>
				</a>
			</div>
		</div>

		<div class="divInfoSistema">
			<div>
				<h1 class="titlePanelControl">Configuración</h1>
			</div>
			<div class="containerPerfil">
				<div class="containerDataUser">
					<div class="logoUser">
					    <img src="img/logoUser.png" alt="">
					</div>
					<div class="divDataUser">
					    <h4>Información personal</h4>
						<div>
						    <label>Nombre: </label><span><?= $_SESSION['nombre']; ?></span>
						</div>
						<div>
						    <label>Correo: </label><span><?= $_SESSION['email']; ?></span>
						</div>

						<h4>Cambiar contraseña</h4>
						<form action="" method="post" name="frmChangePass" id="frmChangePass">
						    <div>
							    <input type="password" name="txtPassUser" id="txtPassUser" placeholder="Contraseña actual" required>
							</div>
							<div>
							    <input class="newPass" type="password" name="txtNewPassUser" id="txtNewPassUser" placeholder="Nueva contraseña" required>
							</div>
							<div>
							    <input class="newPass" type="password" name="txtPassConfirm" id="txtPassConfirm" placeholder="Confirmar contraseña" required>
							</div>
							<br>
							<div class="alertChangePass" style="display: none;"></div>
							<div>
							    <button type="submit" class="btn_save btnChangePass"><i class="fas fa-key"></i> Cambiar contraseña</button>
							</div>
						</form>
					</div>
				</div>
				<?php
				    //Mostrar datos de caja
					    include "../conexion.php";
						$user = $_SESSION['idUser'];
		                $hola = mysqli_query($conexionDB,"SELECT SUM(Total_caja) as total FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$user')");
                        $result = mysqli_num_rows($hola);
						if($result > 0){
							$data = mysqli_fetch_array($hola);
							$igual = $data['total'];
						}
						$conexionDB->close();
				?>
				<div class="containerDataEmpresa">
					<div class="logoUser">
					    <img src="img/logoCaja.png" alt="">
					</div>
					<div class="divDataUser">
					<h4>Información de Caja</h4>
					<div>
						<label>ID Empleado: </label><span><?= $_SESSION['idUser']; ?></span>
					</div>
					<div>
						<label>Rol: </label><span><?= $_SESSION['rol_name']; ?></span>
					</div>
					<div>
					    <label>Usuario: </label><span><?= $_SESSION['user']; ?></span>
					</div>
					<div>
					    <label>Saldo Total: </label><span>S/. <?= $igual; ?></span>
					</div>
				<?php 
				    include "../conexion.php"; 
				    $query = mysqli_query($conexionDB,"SELECT Estado, IdCaja FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$user')");
					$conexionDB->close();
				    $resultado = mysqli_fetch_array($query);
					$estado = $resultado['Estado'];
					if($estado == 'Abierto'){
				?>
				    <div>
						<label>Estado de Caja: </label><span style="color: #fff; background: #60a756; border-radius:5px; padding: 3px 15px;">
						ABIERTA</span>
					</div><br>
				<?php
	                } else {
                ?>
					<div>
						<label>Estado de Caja: </label><span style="color: #fff; background: #f36a6a; border-radius:5px; padding: 3px 15px;">
						CERRADA</span>
					</div><br>
				<?php
                    }
                ?>
					<h4>Datos de Caja</h4>
					<a href="lista_caja.php"><button type="submit" class="btn_save"><i class="fas fa-cash-register"></i> Actividad de Caja</button></a>
					</div>
				</div>
			</div>
		</div>

	</section>
	<?php include "includes/footer.php"; ?>
</body>
</html>