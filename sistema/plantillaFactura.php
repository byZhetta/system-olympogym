<?php
	require("../fpdf/fpdf.php");

	class facturaPDF extends FPDF {
		function Header(){
			// Datos de la factura
			require("../conexion.php");

			$codCliente = $_REQUEST['cl'];
            $noFactura = $_REQUEST['f'];
			$queryventa = mysqli_query($conexionDB,"SELECT f.IdVenta, DATE_FORMAT(f.Fecha, '%d/%m/%Y') as fecha, 
													DATE_FORMAT(f.Fecha,'%H:%i') as  hora, f.Cod_Caja,
													f.Cod_Socio, c.Cod_Empleado, e.Nombre as empleado,
													s.Dni, s.Nombre
													FROM ventas f INNER JOIN socios s ON f.Cod_Socio = s.Id_Socio
													INNER JOIN caja c ON f.Cod_Caja = c.IdCaja
													INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado
													WHERE f.IdVenta = $noFactura AND f.Cod_Socio = $codCliente");
			mysqli_close($conexionDB);
			while ($dataventa = mysqli_fetch_array($queryventa)) {
				$nFactura = $dataventa['IdVenta'];
				$fechav = $dataventa['fecha'];
				$horav = $dataventa['hora'];
				$nombresoc = $dataventa['Nombre'];
				$nombreven = $dataventa['empleado'];
				$dnisoc = $dataventa['Dni'];
			}
			
			// Formato de facturación
			$this->Image('../img/logo_platinium.png',15,15,45);
			$this->Ln(10);
			$this->Cell(103);
			$this->SetFont('Arial','B',13);
			$this->Cell(26,7,mb_convert_encoding('Factura Nº:', 'ISO-8859-1', 'UTF-8'),0,0);
			$this->SetFont('Arial','B',13);
			$this->Cell(20,7,$nFactura,0,0);

			$this->Ln(5);	
			$this->Cell(103);
			$this->setFont('Arial','B',13);
			$this->Cell(26,7,'Fecha:',0,0);
			$this->SetFont('Arial','',13);
			$this->Cell(20,7,$fechav,0,0);

			$this->Ln(5);
			$this->Cell(103);
			$this->setFont('Arial','B',13);
			$this->Cell(26,7,'Hora:',0,0);
			$this->SetFont('Arial','',13);
			$this->Cell(20,7,$horav,0,0);

			$this->Ln(10);
			$this->Cell(189,0,'',1,0,'C',1);
			
			$this->Ln(5);
			$this->SetFont('Arial','B',13);
			$this->Cell(80,8,'Vendedor:',0,0);
			$this->SetFont('Arial','',13);
			$this->Cell(0,8,mb_convert_encoding($nombreven, 'ISO-8859-1', 'UTF-8'),0,1);
			$this->SetFont('Arial','B',13);
			$this->Cell(80,8,'Nombre del Cliente:',0,0);
			$this->SetFont('Arial','',13);
			$this->Cell(0,8,mb_convert_encoding($nombresoc, 'ISO-8859-1', 'UTF-8'),0,1);
			$this->SetFont('Arial','B',13);
			$this->Cell(80,8,'DNI del Cliente:',0,0);
			$this->SetFont('Arial','',13);
			$this->Cell(0,8,$dnisoc,0,1);

			$this->Ln(5);
			$this->Cell(189,0,'',1,0,'C',1);
			$this->Ln(5);

		}
		
		function footer(){
			$this->SetY(-40);
			// el punto Y comenzará desde el punto final de la página, 15 puntos arriba
			$this->SetFont('Arial','I',13);
			// Italic (cursiva)
			$this->Cell(0, 10, mb_convert_encoding('Gracias por su compra !!', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
			// x y utf por el acento
		}
	}
?>