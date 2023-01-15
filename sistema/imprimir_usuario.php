<?php

    // Inserto el código del archivo conxiondb_agenda que tiene la conexión a la base de datos
    require('../conexion.php');

    // si el formulario ha sido enviado procesa los datos del formulario                        
    
    if (isset($_GET['nPdf'])) 
        {
            require("fpdf.php");   
            $pdf= new MiPDF();
            $pdf->AliasNBPages();
                // para que tome el número de página y salga abajo en el footer
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',11);
            $pdf->SetFillColor(232,232,232);
                // color del encabezado
            $pdf->Ln(10);
                // dejo un espacio de 10
            $pdf->Cell(10, 6, 'Id', 1, 0, 'C', 1);
            $pdf->Cell(35, 6, 'Nombre', 1, 0, 'C', 1);
            $pdf->Cell(30, 6, 'Dni', 1, 0, 'C', 1);
            $pdf->Cell(35, 6, 'Direccion', 1, 0, 'C', 1);
            $pdf->Cell(20, 6, 'Telefono', 1, 0, 'C', 1);
            $pdf->Cell(40, 6, 'Email', 1, 0, 'C', 1);
            $pdf->Cell(25, 6, 'Usuario', 1, 1, 'C', 1);
                // son los Títulos de la tabla
                //     ancho/ alto/ texto/ borde/ salto de línea/ Centrado

            $sql = "SELECT * FROM empleados";
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
                    $pdf->Cell(10, 6, $fila["IdEmpleado"], 1, 0, 'C', 1);
                    $pdf->Cell(35, 6, mb_convert_encoding($fila["Nombre"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                    $pdf->Cell(30, 6, $fila["Dni"], 1, 0, 'C', 1);
                    $pdf->Cell(35, 6, mb_convert_encoding($fila["Direccion"], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, $fila["Telefono"], 1, 0, 'C', 1);
                    $pdf->Cell(40, 6, $fila["Email"], 1, 0, 'C', 1);
                    $pdf->Cell(25, 6, $fila["Usuario"], 1, 1, 'C', 1);
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
                header('Content-type:application/vnd.ms-excel; charset-UT-8');
                header('Content-Disposition: attachment;filename=reporteEmpleados.xls');
                header('Pragma: no-cache');
                header('Expires: 0');
                $sql = "SELECT * FROM empleados";
                $queryArticulos = $conexionDB->query($sql);
                if ($queryArticulos->num_rows > 0)
                {
                    echo "<table border-\"0\"; border-color- \"black\">";
                    echo    "<tr style=\"background-color: beige\">";
                    echo        "<td style=\"width:100px\">";
                    echo            "Id";
                    echo        "<td>";
                    echo        "<td style=\"width:300px\">";
                    echo            "Nombre";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Dni";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Dirección";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Telefono";
                    echo        "<td>";
                    echo        "<td style=\"width:200px\">";
                    echo            "Email";
                    echo        "<td>";
                    echo        "<td style=\"width:100px\">";
                    echo            "Usuario";
                    echo        "<td>";
                    echo    "<tr>";
                    while ($fila = $queryArticulos->fetch_assoc())
                    {
                        echo    "<tr>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["IdEmpleado"];
                        echo        "<td>";
                        echo        "<td style=\"width:200px\">";
                        echo             mb_convert_encoding($fila["Nombre"], 'ISO-8859-1', 'UTF-8');
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["Dni"];
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            mb_convert_encoding($fila["Direccion"], 'ISO-8859-1', 'UTF-8');
                        echo        "<td>";
                        echo        "<td style=\"width:100px\">";
                        echo            $fila["Telefono"];
                        echo        "<td>";
                        echo        "<td style=\"width:300px\">";
                        echo            $fila["Email"];
                        echo        "<td>";
                        echo        "<td style=\"width:300px\">";
                        echo            $fila["Usuario"];
                        echo        "<td>";
                        echo    "<tr>";                                                                    
                    }
                    echo    "</table>";                                                                    


                }

            }
        }  
            

?>