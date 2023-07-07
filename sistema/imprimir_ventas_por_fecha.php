<?php

require("fpdf.php"); 
include "../conexion.php";

    if (isset($_GET['nPdf1'])) 
    {
        $fecha_de = $_REQUEST['fecha_de'];
        $fecha_a = $_REQUEST['fecha_a'];

        header('Content-Type: text/html; charset=UTF-8');
        $pdf= new MiPDF();
        $pdf->AliasNBPages();
            // para que tome el número de página y salga abajo en el footer
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(232,232,232);
        // color del encabezado
        $pdf->Ln(10);
            // dejo un espacio de 10
        $pdf->Cell(25, 6, 'Nro. Factura', 1, 0, 'C', 1);
        $pdf->Cell(50, 6, 'Fecha/Hora', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Dni Cliente', 1, 0, 'C', 1);
        $pdf->Cell(35, 6, 'Nom Cliente', 1, 0, 'C', 1);
        $pdf->Cell(35, 6, 'Vendedor', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Total', 1, 1, 'C', 1);
    
        $sql = ("SELECT v.IdVenta, v.Fecha, v.Cod_Caja, v.Cod_Socio, v.Total, 
        s.Nombre as cliente, s.Dni as dnis, c.Cod_Empleado as empl, e.Nombre as nempl
        FROM ventas v INNER JOIN socios s ON v.Cod_Socio = s.Id_Socio 
        INNER JOIN caja c ON v.Cod_Caja = c.IdCaja
        INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado WHERE DATE(v.Fecha) BETWEEN '$fecha_de' AND '$fecha_a'  
        ORDER BY IdVenta DESC");

        $queryArticulos = $conexionDB->query($sql);
        if ($queryArticulos->num_rows > 0)
        {
        
            $pdf->SetFont('Arial','',10);
                // fuente para las filas de la tabla
            $pdf->SetFillColor(255,255,255);
                // fondo blanco para las filas de la tabla

            while ($fila = $queryArticulos->fetch_assoc())

            {
                $fechaexa = date("d-m-Y H:i:s", strtotime($fila["Fecha"]));

                // recorro el query imprimiendo los campos
                $pdf->Cell(25, 6, $fila["IdVenta"], 1, 0, 'C', 1);
                $pdf->Cell(50, 6, $fechaexa, 1, 0, 'C', 1);
                $pdf->Cell(25, 6, $fila["dnis"], 1, 0, 'C', 1);
                $pdf->Cell(35, 6, mb_convert_encoding($fila["cliente"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                $pdf->Cell(35, 6, $fila["empl"].'-'.mb_convert_encoding($fila["nempl"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                $pdf->Cell(20, 6, 'S/. '.$fila["Total"], 1, 1, 'C', 1);
            }

        $pdf->Output('', 'ventas_por_fecha.pdf');
        }
            else
            echo 'No hay artículos para mostrar';
    }
    else
    {
            if (isset($_GET['nExcel1'])) 
            {
                $fecha_de = $_REQUEST['fecha_de'];
                $fecha_a = $_REQUEST['fecha_a'];
                header('Content-type:application/vnd.ms-excel; charset-UTF-8');
                header('Content-Disposition: attachment;filename=Reporte_ventas_por_fecha.xls');
                header('Pragma: no-cache');
                header('Expires: 0');
                $sql = "SELECT v.IdVenta, v.Fecha, v.Cod_Caja, v.Cod_Socio, v.Total, 
                        s.Nombre as cliente, s.Dni as dnis, c.Cod_Empleado as empl, e.Nombre as nempl
                        FROM ventas v INNER JOIN socios s ON v.Cod_Socio = s.Id_Socio 
                        INNER JOIN caja c ON v.Cod_Caja = c.IdCaja
                        INNER JOIN empleados e ON c.Cod_Empleado = e.IdEmpleado WHERE DATE(v.Fecha) BETWEEN '$fecha_de' AND '$fecha_a'
                        ORDER BY IdVenta DESC";
                $queryArticulos = $conexionDB->query($sql);
                if ($queryArticulos->num_rows > 0)
                {
                    echo "<table border-\"0\"; border-color- \"black\">";
                    echo    "<tr style=\"background-color: beige\">";
                    echo        "<td style=\"width:90px\">";
                    echo            "Nro. Factura";
                    echo        "<td>";
                    echo        "<td style=\"width:200px\">";
                    echo            "Fecha/Hora";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Dni Cliente";
                    echo        "<td>";
                    echo        "<td style=\"width:250px\">";
                    echo            "Nom Cliente";
                    echo        "<td>";
                    echo        "<td style=\"width:260px\">";
                    echo            "Vendedor";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Total";
                    echo        "<td>";
                    echo    "<tr>";
                    while ($fila = $queryArticulos->fetch_assoc())
                    {
                        echo    "<tr>";
                        echo        "<td style=\"width:90px\">";
                        echo            $fila["IdVenta"];
                        echo        "<td>";
                        echo        "<td style=\"width:200px\">";
                        echo             date("d-m-Y H:i:s", strtotime($fila["Fecha"]));
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["dnis"];
                        echo        "<td>";
                        echo        "<td style=\"width:250px\">";
                        echo            $fila["cliente"];
                        echo        "<td>";
                        echo        "<td style=\"width:260px\">";
                        echo            $fila["empl"].'-'.$fila["nempl"];
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            ''.$fila["Total"];
                        echo        "<td>";
                        echo    "<tr>";                                                                    
                    }
                    echo    "</table>";                                                                    


                }

            }
    }