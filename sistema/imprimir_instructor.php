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
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFillColor(232,232,232);
                        // color del encabezado
                    $pdf->Ln(10);
                        // dejo un espacio de 10
                    $pdf->Cell(10, 6, utf8_decode('Id'), 1, 0, 'C', 1);
                    $pdf->Cell(35, 6, 'Nombre', 1, 0, 'C', 1);
                    $pdf->Cell(30, 6, 'Dni', 1, 0, 'C', 1);
                    $pdf->Cell(30, 6, 'Direccion', 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, 'Telefono', 1, 0, 'C', 1);
                    $pdf->Cell(40, 6, 'Email', 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, 'Sueldo', 1, 1, 'C', 1);
                        // son los Títulos de la tabla
                        //     ancho/ alto/ texto/ borde/ salto de línea/ Centrado

                    $sql = "SELECT * FROM instructores";
                    $queryArticulos = $conexionDB->query($sql);
                    if ($queryArticulos->num_rows > 0)
                    {
                        $pdf->SetFont('Arial','',7);
                            // fuente para las filas de la tabla
                        $pdf->SetFillColor(255,255,255);
                            // fondo blanco para las filas de la tabla

                        while ($fila = $queryArticulos->fetch_assoc())
                        {
                            // recorro el query imprimiendo los campos
                            $pdf->Cell(10, 6, $fila["Id_Instructor"], 1, 0, 'C', 1);
                            $pdf->Cell(35, 6, $fila["Nombre"], 1, 0, 'C', 1);
                            $pdf->Cell(30, 6, $fila["Dni"], 1, 0, 'C', 1);
                            $pdf->Cell(30, 6, $fila["Direccion"], 1, 0, 'C', 1);
                            $pdf->Cell(20, 6, $fila["Telefono"], 1, 0, 'C', 1);
                            $pdf->Cell(40, 6, $fila["Email"], 1, 0, 'C', 1);
                            $pdf->Cell(20, 6, '$ '.$fila["Sueldo"], 1, 1, 'C', 1);
                        }
                        $pdf->Output('', 'instructores_completo.pdf');
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
                        header('Content-Disposition: attachment;filename=reporteInstructor.xls');
                        header('Pragma: no-cache');
                        header('Expires: 0');
                        $sql = "SELECT * FROM instructores";
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
                            echo            "Teléfono";
                            echo        "<td>";
                            echo        "<td style=\"width:100px\">";
                            echo            "Email";
                            echo        "<td>";
                            echo        "<td style=\"width:100px\">";
                            echo            "Sueldo";
                            echo        "<td>";
                            echo    "<tr>";
                            while ($fila = $queryArticulos->fetch_assoc())
                            {
                                echo    "<tr>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Id_Instructor"];
                                echo        "<td>";
                                echo        "<td style=\"width:300px\">";
                                echo             $fila["Nombre"];
                                echo        "<td>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Dni"];
                                echo        "<td>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Direccion"];
                                echo        "<td>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Telefono"];
                                echo        "<td>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Email"];
                                echo        "<td>";
                                echo        "<td style=\"width:100px\">";
                                echo            $fila["Sueldo"];
                                echo        "<td>";
                                echo    "<tr>";                                                                    
                            }
                            echo    "</table>";                                                                    


                        }

                    }
                }
            
            

?>
