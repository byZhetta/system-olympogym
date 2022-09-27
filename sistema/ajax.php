<?php
 
    include "../conexion.php";
    session_start();

    if(!empty($_POST)){

        //Extraer Artículo - Venta
        if($_POST['action'] == 'infoProducto'){

            $articulo = $_POST['articulo'];

            $query = mysqli_query($conexionDB,"SELECT IdArticulo,Descripcion,Cantidad,Precio_Unitario,Marca FROM articulos WHERE IdArticulo LIKE '$articulo'");

            mysqli_close($conexionDB);

            $result = mysqli_num_rows($query);
            if($result > 0){
                $data = mysqli_fetch_assoc($query);
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
                exit;
            }
            echo 'error';
            exit;
        }

        //Buscar Socio -cliente-
        if($_POST['action'] == 'searchCliente'){
            if(!empty($_POST['cliente'])){

                $dni = $_POST['cliente'];
                $query = mysqli_query($conexionDB,"SELECT * FROM socios WHERE Dni LIKE '$dni'");
    
                mysqli_close($conexionDB);
                $result = mysqli_num_rows($query);
    
                $data = '';
                if($result > 0){
                    $data = mysqli_fetch_assoc($query);
                } else {
                    $data = 0;
                }
                echo json_encode($data,JSON_UNESCAPED_UNICODE);
            }
            exit;
        }

        //Registra Cliente - ventas
        if($_POST['action'] == 'addCliente'){
            $dni = $_POST['dni_cliente'];
            $nombre = $_POST['nom_cliente'];
            $telefono = $_POST['tel_cliente'];
            $direccion = $_POST['dir_cliente'];
            $correo = $_POST['cor_cliente'];

            $query_insert = mysqli_query($conexionDB,"INSERT INTO socios (Dni, Nombre, Telefono, Direccion, Email)
                                                        VALUES ('$dni','$nombre','$telefono','$direccion','$correo')");
            if($query_insert){
                $codCliente = mysqli_insert_id($conexionDB);
                $msg = $codCliente;
            } else {
                $msg = 'error';
            }
            mysqli_close($conexionDB);
            echo $msg;
            exit;
        }

        //Agregar producto al detalle temporal
        if($_POST['action'] == 'addProductDetalle'){
            if(empty($_POST['producto']) || empty($_POST['cantidad'])){
                echo 'error';
            } else {
                $codArticulo = $_POST['producto'];
                $cantidad = $_POST['cantidad'];

                $query_detalle_temp = mysqli_query($conexionDB,"CALL add_detalle_temp($codArticulo,$cantidad)");
                $result = mysqli_num_rows($query_detalle_temp);

                $detalleTabla = '';
                $sub_total = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    while ($data = mysqli_fetch_assoc($query_detalle_temp)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <th>'.$data['codArticulo'].'</th>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textright">'.$precioTotal.'</td>
                                            <td class="">
                                                <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>';
                    }
                    
                    $detalleTotales = ' <tr>
                                            <td colspan="5" class="textright">SUBTOTAL</td>
                                            <td class="textright">'.$sub_total.'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">TOTAL</td>
                                            <td class="textright">'.$total.'</td>
                                        </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                } else {
                    echo 'error';
                }
                mysqli_close($conexionDB);
            }
            exit;
        }

        //Extraer datos del detalle_temp
        if($_POST['action'] == 'searchForDetalle'){
            if(empty($_POST['user'])){
                echo 'error';
            } else {

                $query = mysqli_query($conexionDB,"SELECT tmp.correlativo,
                                                          tmp.cantidad,
                                                          tmp.precio_venta,
                                                          tmp.codArticulo,
                                                          a.descripcion
                                                    FROM detalle_temp tmp
                                                    INNER JOIN articulos a
                                                    ON tmp.codArticulo = a.IdArticulo");

                $result = mysqli_num_rows($query);

                $detalleTabla = '';
                $sub_total = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    while ($data = mysqli_fetch_assoc($query)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <th>'.$data['codArticulo'].'</th>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textright">'.$precioTotal.'</td>
                                            <td class="">
                                                <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>';
                    }
                    
                    $detalleTotales = ' <tr>
                                            <td colspan="5" class="textright">SUBTOTAL</td>
                                            <td class="textright">'.$sub_total.'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">TOTAL</td>
                                            <td class="textright">'.$total.'</td>
                                        </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                } else {
                    echo 'error';
                }
                mysqli_close($conexionDB);
            }
            exit;
        }
        
        //Extraer datos del detalle_temp
        if($_POST['action'] == 'delProductDetalle'){
            if(empty($_POST['id_detalle'])){
                echo 'error';
            } else {

                $id_detalle = $_POST['id_detalle'];

                $query_detalle_temp = mysqli_query($conexionDB,"CALL del_detalle_temp($id_detalle)");
                $result = mysqli_num_rows($query_detalle_temp);

                $detalleTabla = '';
                $sub_total = 0;
                $total = 0;
                $arrayData = array();

                if($result > 0){
                    while ($data = mysqli_fetch_assoc($query_detalle_temp)){
                        $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                        $sub_total = round($sub_total + $precioTotal, 2);
                        $total = round($total + $precioTotal, 2);

                        $detalleTabla .= '<tr>
                                            <th>'.$data['codArticulo'].'</th>
                                            <td colspan="2">'.$data['descripcion'].'</td>
                                            <td class="textcenter">'.$data['cantidad'].'</td>
                                            <td class="textright">'.$data['precio_venta'].'</td>
                                            <td class="textright">'.$precioTotal.'</td>
                                            <td class="">
                                                <a class="link_delete" href="#" onclick="event.preventDefault(); del_product_detalle('.$data['correlativo'].');"><i class="far fa-trash-alt"></i></a>
                                            </td>
                                        </tr>';
                    }
                    
                    $detalleTotales = ' <tr>
                                            <td colspan="5" class="textright">SUBTOTAL</td>
                                            <td class="textright">'.$sub_total.'</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="textright">TOTAL</td>
                                            <td class="textright">'.$total.'</td>
                                        </tr>';

                    $arrayData['detalle'] = $detalleTabla;
                    $arrayData['totales'] = $detalleTotales;

                    echo json_encode($arrayData,JSON_UNESCAPED_UNICODE);
                } else {
                    echo 'error';
                }
                mysqli_close($conexionDB);
            }
            exit;
        }

        //Anular venta
        if($_POST['action'] == 'anularVenta'){

            $query_del = mysqli_query($conexionDB,"DELETE FROM detalle_temp");
            mysqli_close($conexionDB);

            if($query_del){
                echo 'ok';
            } else {
                echo 'error';
            }
            exit;
        }

        //Procesar Venta
        if($_POST['action'] == 'procesarVenta'){

            //ID de usuario
            $usuario = $_SESSION['idUser'];
            $codcliente = $_POST['codcliente'];
            
            $query = mysqli_query($conexionDB,"SELECT * FROM detalle_temp");
            $result = mysqli_num_rows($query);

            if($result > 0){
                $query_procesar = mysqli_query($conexionDB,"CALL procesar_venta($usuario,$codcliente)");
                $result_detalle = mysqli_num_rows($query_procesar);

                if($result_detalle > 0){
                    $data = mysqli_fetch_assoc($query_procesar);
                    echo json_encode($data,JSON_UNESCAPED_UNICODE);
                } else {
                    echo "error";
                }
            } else {
                echo "error";
            }
            mysqli_close($conexionDB);
            exit;
        }

        //Cambiar contraseña
        if($_POST['action'] == 'changePassword'){
            if (!empty($_POST['passActual']) && !empty($_POST['passNuevo'])){
                $password = md5($_POST['passActual']);
                $newPass = md5($_POST['passNuevo']);
                $idUser = $_SESSION['idUser'];

                $code = '';
                $msg = '';
                $arrData = array();

                $query_user = mysqli_query($conexionDB,"SELECT * FROM empleados
                                                        WHERE Clave = '$password' and IdEmpleado = $idUser");
                $result = mysqli_num_rows($query_user);
                if($result > 0){
                    $query_update = mysqli_query($conexionDB,"UPDATE empleados SET Clave = '$newPass' WHERE IdEmpleado = $idUser");
                    mysqli_close($conexionDB);

                    if($query_update){
                        $code = '00';
                        $msg = "Su contraseña se ha actualizado con éxito.";
                    } else {
                        $code = '2';
                        $msg = "No es posible cambiar su contraseña.";
                    }
                } else {
                    $code = '1';
                    $msg = "La contraseña actual es incorrecta.";
                }
                $arrData = array('cod' => $code, 'msg' => $msg);
                echo json_encode($arrData,JSON_UNESCAPED_UNICODE);                                        

            } else {
                echo "error";
            }
            exit;
        }

    }
    exit;

    
?>