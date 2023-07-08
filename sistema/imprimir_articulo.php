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
            $pdf->SetFont('Arial','B',12);
            $pdf->SetFillColor(232,232,232);
                // color del encabezado
            $pdf->Ln(10);
                // dejo un espacio de 10
            $pdf->Cell(25, 6, mb_convert_encoding('Código', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
            $pdf->Cell(65, 6, 'Descripcion', 1, 0, 'C', 1);
            $pdf->Cell(35, 6, 'Marca', 1, 0, 'C', 1);
            $pdf->Cell(25, 6, 'Cantidad', 1, 0, 'C', 1);
            $pdf->Cell(35, 6, 'Precio Unitario', 1, 1, 'C', 1);
                // son los Títulos de la tabla
                //     ancho/ alto/ texto/ borde/ salto de línea/ Centrado

            $sql = "SELECT IdArticulo, Descripcion, Cantidad, Precio_Unitario, Marca FROM articulos";
            $queryArticulos = $conexionDB->query($sql);
            if ($queryArticulos->num_rows > 0)
            {
                $pdf->SetFont('Arial','',10);
                    // fuente para las filas de la tabla
                $pdf->SetFillColor(255,255,255);
                    // fondo blanco para las filas de la tabla

                while ($fila = $queryArticulos->fetch_assoc())
                {
                    // recorro el query imprimiendo los campos
                    $pdf->Cell(25, 6, $fila["IdArticulo"], 1, 0, 'C', 1);
                    $pdf->Cell(65, 6, mb_convert_encoding($fila["Descripcion"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                    $pdf->Cell(35, 6, $fila["Marca"], 1, 0, 'C', 1);
                    $pdf->Cell(25, 6, $fila["Cantidad"], 1, 0, 'C', 1);
                    $pdf->Cell(35, 6, 'S/. '.$fila["Precio_Unitario"], 1, 1, 'C', 1);
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
                header('Content-Disposition: attachment;filename=reporteArticulos.xls');
                header('Pragma: no-cache');
                header('Expires: 0');
                $sql = "SELECT IdArticulo, Descripcion, Cantidad, Precio_Unitario, Marca FROM articulos";
                $queryArticulos = $conexionDB->query($sql);
                if ($queryArticulos->num_rows > 0)
                {
                    echo "<table border-\"0\"; border-color- \"black\">";
                    echo    "<tr style=\"background-color: beige\">";
                    echo        "<td style=\"width:100px\">";
                    echo            mb_convert_encoding("Código", 'ISO-8859-1', 'UTF-8');
                    echo        "<td>";
                    echo        "<td style=\"width:300px\">";
                    echo            "Descripcion";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Marca";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Cantidad";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Precio/U";
                    echo        "<td>";
                    echo    "<tr>";
                    while ($fila = $queryArticulos->fetch_assoc())
                    {
                        echo    "<tr>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["IdArticulo"];
                        echo        "<td>";
                        echo        "<td style=\"width:300px\">";
                        echo             mb_convert_encoding($fila["Descripcion"], 'ISO-8859-1', 'UTF-8');
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["Marca"];
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["Cantidad"];
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["Precio_Unitario"];
                        echo        "<td>";
                        echo    "<tr>";                                                                    
                    }
                    echo    "</table>";                                                                    


                }

            }
        }
              
           

?>