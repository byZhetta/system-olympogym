
        <nav>
			<ul>
				<li><a href="index.php">Inicio</a></li>
				<?php if($_SESSION['rol'] == 1){ ?>		
				<li class="principal">
					<a href="#">Membresias <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_clase.php">Nuevo Membresia</a></li>
						<li><a href="clases.php">Lista de Membresias</a></li>
					</ul>
				</li>
					<?php } ?>
				<?php if($_SESSION['rol'] == 1){ ?>	
					
				<li class="principal">
					<a href="#">Usuarios <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_usuario.php">Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php">Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php } ?>
				<li class="principal">
					<a href="#">Socios <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_socio.php">Nuevo Socio</a></li>
						<li><a href="lista_socio.php">Lista de Socios</a></li>
					</ul>
				</li>
				<?php if($_SESSION['rol'] == 1){ ?>
				<li class="principal">
					<a href="#">Instructores <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_instructor.php">Nuevo Instructor</a></li>
						<li><a href="lista_instructores.php">Lista de Instructores</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#">Proveedores <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_proveedor.php">Nuevo Proveedor</a></li>
						<li><a href="lista_proveedores.php">Lista de Proveedores</a></li>
					</ul>
				</li>
				<?php } ?>
				<li class="principal">
					<a href="#">Artículos <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_articulo.php">Nuevo Artículo</a></li>
						<li><a href="lista_articulos.php">Lista de Artículos</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#">Ventas <span class="arrow"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="ventaArticulos.php">Venta Artículo</a></li>
						<li><a href="ventaServicios.php">Venta Servicio</a></li>
						<li><a href="ventas.php">Historial de Ventas</a></li>
					</ul>
				</li>
			</ul>
		</nav>
