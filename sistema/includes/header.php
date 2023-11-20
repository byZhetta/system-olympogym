<?php

    if(empty($_SESSION['active'])) {
	    header('location: ../');
    }

?>

    <header>
		<div class="header">
			<a href="#" class="btnMenu"><i class="fas fa-bars"></i></a>
		    <img class="logo" src="../img/logo_platinium1.png" alt="Platinium Fit" width="300px" heigth="180px">
			<div class="optionsBar">
				<!---
			    <p><?php echo fechaC(); ?></p>
				<span>|</span>--->
				<span class="user"><?php echo $_SESSION['user']; ?></span>
				<span class="photouser" ><i class="far fa-user-circle fa-2x"></i></span>
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include "nav.php"; ?>
	</header>