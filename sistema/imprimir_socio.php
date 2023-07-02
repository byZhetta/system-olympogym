<?php

    // Inserto el código del archivo conxiondb_agenda que tiene la conexión a la base de datos
    require('../conexion.php');

    // si el formulario ha sido enviado procesa los datos del formulario                        
    
    if (isset($_GET['nPdf'])) 
        {
            require("fpdf.php");
            header('Content-Type: text/html; charset=UTF-8');   
            $pdf= new MiPDF();
            $pdf->AliasNBPages();
                // para que tome el número de página y salga abajo en el footer
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',11);
            $pdf->SetFillColor(232,232,232);
                // color del encabezado
            $pdf->Ln(10);
                // dejo un espacio de 10
            $pdf->Cell(12, 6, 'Nro.', 1, 0, 'C', 1);
            $pdf->Cell(45, 6, 'Nombre', 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'Dni', 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'Telefono', 1, 0, 'C', 1);
            $pdf->Cell(30, 6, mb_convert_encoding('Membresía', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'F. Inicio', 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'F. Final', 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'Estado', 1, 1, 'C', 1);
                // son los Títulos de la tabla
                //     ancho/ alto/ texto/ borde/ salto de línea/ Centrado

            $sql = "SELECT * FROM socios s
                    INNER JOIN clases c ON s.Id_Clase = c.IdClase";
            $queryArticulos = $conexionDB->query($sql);
            if ($queryArticulos->num_rows > 0)
            {
                $pdf->SetFont('Arial','',8);
                    // fuente para las filas de la tabla
                $pdf->SetFillColor(255,255,255);
                    // fondo blanco para las filas de la tabla

                while ($fila = $queryArticulos->fetch_assoc())
                {
                    // recorro el query imprimiendo los campos
                    $pdf->Cell(12, 6, $fila["Id_Socio"], 1, 0, 'C', 1);
                    $pdf->Cell(45, 6, mb_convert_encoding($fila["Nombre"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, $fila["Dni"], 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, $fila["Telefono"], 1, 0, 'C', 1);
                    $pdf->Cell(30, 6, $fila["NombreC"], 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, $fila["fecha_ingreso"], 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, $fila["fecha_vencimiento"], 1, 0, 'C', 1);
                    if(date('Y-m-d') > $fila['fecha_vencimiento']){
                        $pdf->Cell(20, 6, 'Vencido', 1, 1, 'C', 1);
                    }else{
                        $pdf->Cell(20, 6, 'Activo', 1, 1, 'C', 1);
                    }
                }
                $pdf->Output('', 'articulos_completo.pdf');
                // acá mando la salida y con nombre por defecto como "articulos_completo.pdf"
                // primer parámetro: nada: muestra el archivo, D muestra para descargarlo
            }
            else    
                echo 'No hay artículos para mostrar';
        }
        else
        {
            if (isset($_GET['nExcel'])) 
            {
                header('Content-type:application/vnd.ms-excel; charset-UTF-8');
                header('Content-Disposition: attachment;filename=reporteSocio.xls');
                header('Pragma: no-cache');
                header('Expires: 0');
                $sql = "SELECT * FROM socios s
                        INNER JOIN clases c ON s.Id_Clase = c.IdClase";
                $queryArticulos = $conexionDB->query($sql);
                if ($queryArticulos->num_rows > 0)
                {
                    echo "<table border-\"0\"; border-color- \"black\">";
                    echo    "<tr style=\"background-color: beige\">";
                    echo        "<td style=\"width:50px\">";
                    echo            "Nro.";
                    echo        "<td>";
                    echo        "<td style=\"width:200px\">";
                    echo            "Nombre";
                    echo        "<td>";
                    echo        "<td style=\"width:80px\">";
                    echo            "Dni";
                    echo        "<td>";
                    echo        "<td style=\"width:80px\">";
                    echo            mb_convert_encoding("Teléfono", 'ISO-8859-1', 'UTF-8');
                    echo        "<td>";
                    echo        "<td style=\"width:80px\">";
                    echo            mb_convert_encoding("Membresía", 'ISO-8859-1', 'UTF-8');
                    echo        "<td>";
                    echo        "<td style=\"width:80px\">";
                    echo            "F. Inicio";
                    echo        "<td>";
                    echo        "<td style=\"width:80px\">";
                    echo            "F. Final";
                    echo        "<td>";
                    echo        "<td style=\"width:60px\">";
                    echo            "Estado";
                    echo        "<td>";
                    echo    "<tr>";
                    while ($fila = $queryArticulos->fetch_assoc())
                    {
                        echo    "<tr>";
                        echo        "<td style=\"width:50px\">";
                        echo            $fila["Id_Socio"];
                        echo        "<td>";
                        echo        "<td style=\"width:200px\">";
                        echo             mb_convert_encoding($fila["Nombre"], 'ISO-8859-1', 'UTF-8');
                        echo        "<td>";
                        echo        "<td style=\"width:50px\">";
                        echo            $fila["Dni"];
                        echo        "<td>";
                        echo        "<td style=\"width:50px\">";
                        echo            $fila["Telefono"];
                        echo        "<td>";
                        echo        "<td style=\"width:50px\">";
                        echo            mb_convert_encoding($fila["NombreC"], 'ISO-8859-1', 'UTF-8');
                        echo        "<td>";
                        echo        "<td style=\"width:50px\">";
                        echo            $fila["fecha_ingreso"];
                        echo        "<td>";
                        echo        "<td style=\"width:50px\">";
                        echo            $fila["fecha_vencimiento"];
                        echo        "<td>";
                        if(date('Y-m-d') > $fila['fecha_vencimiento']){
                            echo        "<td style=\"width:50px\">";
                            echo            'Vencido';
                            echo        "<td>";
                        }else{
                            echo        "<td style=\"width:50px\">";
                            echo            'Activo';
                            echo        "<td>";
                        }
                        
                        echo    "<tr>";                                                                    
                    }
                    echo    "</table>";                                                                    


                }

            }
        }
              
            
?>