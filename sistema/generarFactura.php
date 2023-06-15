<?php

	require('../conexion.php');
	require('plantillaFactura.php');
	header('Content-Type: text/html; charset=UTF-8');
	
	$noFactura = $_REQUEST['f']; 
	$pdf = new facturaPDF();

    // agregar una página
    $pdf->AddPage();

	$pdf->SetFillColor(210,210,210);
	$pdf->SetFont('Arial','B',10);
	
    // son los Títulos de la tabla
	$pdf->Cell(17,8,mb_convert_encoding('Código', 'ISO-8859-1', 'UTF-8'),1,0,'C',1);
    $pdf->Cell(99,8,mb_convert_encoding('Descripción', 'ISO-8859-1', 'UTF-8'),1,0,'C',1);
    $pdf->Cell(17,8,'Cantidad',1,0,'C',1);
    $pdf->Cell(28,8,'Precio Unitario',1,0,'C',1);
    $pdf->Cell(28,8,'Importe',1,1,'C',1);

	$sqlp = "SELECT a.IdArticulo, a.Descripcion, a.Marca, dt.Cantidad, dt.precio_venta, 
					(dt.Cantidad * dt.precio_venta) as precio_total, f.Total FROM ventas f
					INNER JOIN detallefactura dt ON f.IdVenta = dt.nroFactura
					INNER JOIN articulos a ON dt.CodArticulo = a.IdArticulo
					WHERE f.IdVenta = $noFactura";
	$queryprod = $conexionDB->query($sqlp);

	$sqlm = "SELECT cl.IdClase, cl.NombreC, cl.Dias, DATE_FORMAT(cl.Hora,'%H:%i') as Hora, 
					DATE_FORMAT(cl.Duracion,'%H:%i') as Duracion, cl.Costo_Clase, 
					dts.Periodo, DATE_FORMAT(dts.Fecha_Alta,'%d-%m-%Y') as FechaAlta, 
					DATE_FORMAT(dts.Fecha_Vencim,'%d-%m-%Y') as FechaBaja, f.Total FROM ventas f 
					INNER JOIN detalle_venta_servicios dts ON f.IdVenta = dts.Cod_Venta
					INNER JOIN clases cl ON dts.Cod_Clase = cl.IdClase    
					WHERE f.IdVenta	= $noFactura";
	$querymemb = $conexionDB->query($sqlm);

    if ($queryprod->num_rows > 0) {
		// FACTURACIÓN INFERIOR PARA VENTA DE ARTICULOS
        $pdf->SetFont('Arial','',9);
        // fuente para las filas de la tabla
        $pdf->SetFillColor(255,255,255);
        // fondo blanco para las filas de la tabla

        while ($fila = $queryprod->fetch_assoc()) {
            $pdf->Cell(17,6,$fila["IdArticulo"],1,0,'C',1);
            $pdf->Cell(99,6,mb_convert_encoding($fila["Descripcion"], 'ISO-8859-1', 'UTF-8').' - '.$fila["Marca"],1,0,'',1);
            $pdf->Cell(17,6,$fila["Cantidad"],1,0,'C',1);
            $pdf->Cell(28,6,'S/. '.$fila["precio_venta"],1,0,'R',1);
            $pdf->Cell(28,6,'S/. '.$fila["precio_total"],1,1,'R',1);
            $total = $fila["Total"];
        }
        
        $pdf->Ln(105);
        $pdf->SetFont('Arial','B',10);
		$pdf->Cell(130);
        $pdf->SetFillColor(210,210,210);
        $pdf->Cell(30,6,'Importe Total',1,0,'',1);
		$pdf->SetFillColor(255,255,255);
        $pdf->Cell(30,6,'S/. '.$total,1,1,'R',1);
        
        $pdf->Output('', 'Factura Electronica N° '.$noFactura.'.pdf');
    } else {
        // FACTURACIÓN INFERIOR PARA VENTA DE MEMBRESIAS
        $pdf->SetFont('Arial','',9);
        // fuente para las filas de la tabla
        $pdf->SetFillColor(255,255,255);
        // fondo blanco para las filas de la tabla

        while ($fila = $querymemb->fetch_assoc()) {
            $pdf->Cell(17,12,$fila["IdClase"],1,0,'C',1);
            $pdf->Cell(99,6,mb_convert_encoding($fila["NombreC"], 'ISO-8859-1', 'UTF-8').' - Periodo: '.$fila["Periodo"],1,0,'',1);
            $pdf->Cell(17,6,' 1 ',1,0,'C',1);
            $pdf->Cell(28,6,'S/. '.$fila["Costo_Clase"],1,0,'R',1);
            $pdf->Cell(28,6,'S/. '.$fila["Total"],1,1,'R',1);
			$pdf->Cell(17);
            $pdf->Cell(58,6,'Desde: '.$fila["FechaAlta"].' Hasta: '.$fila["FechaBaja"],1,0,'C',1);
            $pdf->Cell(64,6,$fila["Dias"],1,0,'C',1);
            $pdf->Cell(50,6,'Hora: '.$fila["Hora"].' Tiempo: '.$fila["Duracion"],1,0,'C',1);
            $total = $fila["Total"];
        }
        
        $pdf->Ln(105);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(130);
        $pdf->SetFillColor(210,210,210);
        $pdf->Cell(30,6,'Importe Total',1,0,'',1);
		$pdf->SetFillColor(255,255,255);
        $pdf->Cell(30,6,'S/. '.$total,1,1,'R',1);
        
        $pdf->Output('', 'Factura Electronica N° '.$noFactura.'.pdf');
    }
	
?>