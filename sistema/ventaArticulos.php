<?php 
    include "../conexion.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scripts.php"; ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olympo gym | Venta de Artículos</title>
</head>
<body>
    <?php include "includes/header.php"; ?>
    <section id="container">
        <?php
            $usuario = $_SESSION['idUser']; 
            $query = mysqli_query($conexionDB,"SELECT Estado, IdCaja FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja WHERE Cod_Empleado = '$usuario')");
            $resultado = mysqli_fetch_array($query);
            $estado = $resultado['Estado'];
            if($estado == 'Abierto'){
        ?>
        <div class="title_page">
            <h1>Nueva Venta de Artículo</h1>
        </div>
        <div class="datos_cliente">
            <div class="action_cliente">
                <h4>Datos del Socio</h4>
                <a href="#" class="btn_new btn_new_cliente"><i class="fas fa-plus"></i> Nuevo Socio</a>
            </div>
            <form action="" name="form_new_cliente_venta" id="form_new_cliente_venta" class="datos">
                <input type="hidden" name="action" value="addCliente">
                <input type="hidden" id="idcliente" name="idcliente" value="" required>
                <div class="wd30">
                    <label>DNI</label>
                    <input type="number" name="dni_cliente" id="dni_cliente">
                </div>
                <div class="wd30">
                    <label>Nombre Completo</label>
                    <input type="text" name="nom_cliente" id="nom_cliente" disabled required>
                </div>
                <div class="wd30">
                    <label>Teléfono</label>
                    <input type="number" name="tel_cliente" id="tel_cliente" disabled required>
                </div>
                <div class="wd40">
                    <label>Dirección</label>
                    <input type="text" name="dir_cliente" id="dir_cliente" disabled required>
                </div>
                <div class="wd40">
                    <label>Correo Eléctronico</label>
                    <input type="text" name="cor_cliente" id="cor_cliente" disabled required>
                </div>
                <div id="div_registro_cliente" class="wd100">
                    <button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar</button>
                </div>                
            </form>
        </div>
        <div class="datos_venta">
            <h4>Datos de Venta</h4>
            <div class="datos">
                <div class="wd50">
                    <label>Vendedor</label>
                    <p><?php echo $_SESSION['nombre']; ?></p>
                </div>
                <div class="wd50">
                    <label>Acciones</label>
                    <div id="acciones_venta">
                        <a href="#" class="btn_ok textcenter" id="btn_anular_venta"><i class="fas fa-ban"></i> Anular</a>
                        <a href="#" class="btn_new textcenter" id="btn_facturar_venta" style="display: none;"><i class="fas fa-edit"></i> Procesar</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="datos_venta">
            <div class="datos">
                <div class="wd60">
                    <label>Busqueda de Artículo</label>
                    <input type="text" class="form-control" id="buscar" name="buscar" placeholder="Describa el artículo de busqueda">
                </div>
                <div class="wd30">
                    <button  class="btn btn_busventa" onclick="buscar_ahora($('#buscar').val());"><i class="fas fa-search"></i></button>
                </div>
                <div id="datos_buscador"></div>  
            </div> 
        </div>
        <div class="datos_venta">
            <h4>Artículos</h4>
        </div>
        <table class="tbl_venta">
            <thead>
                <tr>
                    <th width="100px">Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th width="100px">Cantidad</th>
                    <th width="textright">Precio</th>
                    <th width="textright">Precio Total</th>
                    <th>Acción</th>
                </tr>
                <tr>
                    <td><input type="text" name="txt_cod_producto" id="txt_cod_producto"></td>
                    <td id="txt_descripcion">-</td>
                    <td id="txt_existencia">-</td>
                    <td><input type="text" name="txt_cant_producto" id="txt_cant_producto" value="0" min="1" disabled></td>
                    <td id="txt_precio" class="textright">0.00</td>
                    <td id="txt_precio_total" class="textright">0.00</td>
                    <td><a href="#" id="add_product_venta" class="link_add"><i class="fas fa-plus"></i> Agregar</a></td>
                </tr>
                <tr>
                    <th>Código</th>
                    <th colspan="2">Descripción</th>
                    <th>Cantidad</th>
                    <th class="textright">Precio</th>
                    <th class="textright">Precio Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody id="detalle_venta">
                <!--- CONTENIDO AJAX --->
            </tbody>
            <tfoot id="detalle_totales">
                <!--- CONTENIDO AJAX --->
            </tfoot>
        </table>
        <br>
        <br>
        <?php
	        } else {
        ?>
                <div class="data_delete">
                    <i class="fas fa-cash-register fa-7x" style="color: #e66262"></i>
                    <br>
                    <h1 style="color: #ff1a1a; font-size: 25px;">DEBE ABRIR CAJA PARA INICIAR LA VENTA</h1>
                    <br>
                    <br>
                    <a href="lista_caja.php"><button type="submit" class="btn_save"><i class="fas fa-cash-register"></i> Actividad de Caja</button></a>
                </div>
        <?php
            }
        ?>
    </section>
    <?php include "includes/footer.php"; ?>

    <script type="text/javascript">
        $(document).ready(function(){
            var usuarioid = '<?php echo $_SESSION['idUser']; ?>';
            searchForDetalle(usuarioid);
        });

        function buscar_ahora(buscar) {
        var parametros = {"buscar":buscar};
            $.ajax({
                data:parametros,
                type: 'POST',
                url: 'buscarArticulos.php',
                success: function(data) {
                    document.getElementById("datos_buscador").innerHTML = data;
                }
            });
        }
    </script>
    
</body>
</html>