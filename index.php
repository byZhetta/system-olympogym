<?php

	$alert = '';
	// Inserta el archivo de conexión
	require('conexion.php');
	session_start();
	if(!empty($_SESSION['active'])) {
		header('location: sistema/');
	} else {
	
		$confirmado = false;
		// Si el formulario fue enviado, procesa los datos
		if (isset($_POST['submit'])) {
			if ($conexionDB === false) {
				die("ERROR: No fue posible conectarse con la base de datos. " . mysqli_connect_error());
			}

			$inputError = false;
			// recupera y verifica el nombre de usuario recibido.
			if (empty($_POST['Usuario'])) {
				$alert = 'ERROR: Por favor ingrese un usuario válido';
				$inputError = true;
			} else {
				$Usuario = $conexionDB->escape_string($_POST['Usuario']);
			}
			// recupera y verifica la contraseña recibida
			if ($inputError != true && empty($_POST['Clave'])) {
				$alert = 'ERROR: Por favor ingrese una clave válida';
				$inputError = true;
			} else {
				$Clave = md5($conexionDB->escape_string($_POST['Clave']));
			}

			$query = mysqli_query($conexionDB,"SELECT u.IdEmpleado,u.Nombre,u.Dni,u.Email,u.Usuario,r.IdRol,r.rol
	    	                                    FROM empleados u
												INNER JOIN rol r
												ON u.Rol = r.IdRol
												WHERE u.Usuario = '$Usuario' AND u.Clave = '$Clave'");
			$result = mysqli_num_rows($query);

			// Si se encuentra una coincidencia, entonces el usuario es autenticado.
			if($result > 0) {
				$confirmado = true;

				$data = mysqli_fetch_array($query);

        		//Crea variables de sesion.
				$_SESSION['active'] = $confirmado;
				$_SESSION['idUser'] = $data['IdEmpleado'];
				$_SESSION['nombre'] = $data['Nombre'];
				$_SESSION['dni']    = $data['Dni'];
				$_SESSION['email']  = $data['Email'];
    			$_SESSION['user']   = $data['Usuario'];
				$_SESSION['rol']    = $data['IdRol'];
				$_SESSION['rol_name'] = $data['rol'];

				// Muestra el menú
				header('location: sistema/');
			} else {
				$confirmado = false;

				if ($inputError != true) {
					$alert = 'El usuario o la clave son incorrectos!!';
					session_destroy();
				}
			}
			// cierra conexión
			$conexionDB->close();
		}

		// Si no está confirmado imprime el formulario
		if (!$confirmado) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympo gym | Login</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
	<link rel="icon" href="img/pesas.ico" type="image/ico">
	<link href="css/style.css" rel="stylesheet">
</head>
<body>
    <section id="container">
        <form class="formulario" method="post" action="">
			<h3>INICIAR SESIÓN</h3>    
			<div class="contenedor">
			    <img src="img/olympo_gym.png" alt="Login">
				<div class="input-contenedor">
				    <i class="fas fa-user icon"></i>
				    <input type="text" name="Usuario" placeholder="Usuario" />
				</div>
			    <div class="input-contenedor">
			        <i class="fas fa-key icon"></i>
			        <input type="password" name="Clave" placeholder="Contraseña" />
			    </div>
				<h5>Administrador</h5>
				<p>Usuario: [admin1] - Contraseña: [password]</p>
				<h5>Vendedor</h5>
				<p>Usuario: [user1] - Contraseña: [user]</p>
				<div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div><br>
				<input type="submit" name="submit" value="Ingresar" class="button" />
			</div>
		</form>
    </section>
<?php
		}
	}
?>
</body>
</html>