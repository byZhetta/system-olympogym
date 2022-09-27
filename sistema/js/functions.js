//Eventos js 

$(document).ready(function() {
    
    $('.btnMenu').click(function(e){
        e.preventDefault();
        if($('nav').hasClass('viewMenu')){
            $('nav').removeClass('viewMenu');
        } else {
            $('nav').addClass('viewMenu');
        }
    });

    $('nav ul li').click(function(){
        $('nav ul li ul').slideUp();
        $(this).children('ul').slideToggle();
    });

    $('#search_proveedor').change(function(e){
        e.preventDefault();
        var sistema = getUrl();
        location.href = sistema+'buscar_articulo.php?proveedor='+$(this).val();
    });

    //Activar campos para registrar cliente
    $('.btn_new_cliente').click(function(e){
        e.preventDefault();
        $('#nom_cliente').removeAttr('disabled');
        $('#tel_cliente').removeAttr('disabled');
        $('#dir_cliente').removeAttr('disabled');
        $('#cor_cliente').removeAttr('disabled');

        $('#div_registro_cliente').slideDown();
    });

    //Buscar Cliente -socio-
    $('#dni_cliente').keyup(function(e){
        e.preventDefault();

        var cl = $(this).val();
        var action = 'searchCliente';

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,cliente:cl},

            success: function(response){

                if(response == 0){
                    $('#idcliente').val('');
                    $('#nom_cliente').val('');
                    $('#tel_cliente').val('');
                    $('#dir_cliente').val('');
                    $('#cor_cliente').val('');
                    //Mostrar boton agregar
                    $('.btn_new_cliente').slideDown();
                } else {
                    var data = $.parseJSON(response);
                    $('#idcliente').val(data.Id_Socio);
                    $('#nom_cliente').val(data.Nombre);
                    $('#tel_cliente').val(data.Telefono);
                    $('#dir_cliente').val(data.Direccion);
                    $('#cor_cliente').val(data.Email);
                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();

                    //Bloque campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    $('#cor_cliente').attr('disabled','disabled');

                    //Ocultar boton guardar
                    $('#div_registro_cliente').slideUp();

                }
            },
            error: function(error){

            }
        });
    });

    //Crear cliente - Ventas
    $('#form_new_cliente_venta').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: $('#form_new_cliente_venta').serialize(),

            success: function(response){
                if(response != 'error'){
                    //Agregar id a input hidden
                    $('#idcliente').val(response);
                    //Bloque campos
                    $('#nom_cliente').attr('disabled','disabled');
                    $('#tel_cliente').attr('disabled','disabled');
                    $('#dir_cliente').attr('disabled','disabled');
                    $('#cor_cliente').attr('disabled','disabled');

                    //Ocultar boton agregar
                    $('.btn_new_cliente').slideUp();
                    //Ocultar boton guardar
                    $('#div_registro_cliente').slideUp();
                }
            },
            error: function(error){

            }
        });
    });

    //Buscar Producto - Ventas
    $('#txt_cod_producto').keyup(function(e){
        e.preventDefault();

        var producto = $(this).val();
        var action = 'infoProducto';

        if(producto != ''){
            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action,articulo:producto},
    
                success: function(response){
                    if(response != 'error'){
                        var info = JSON.parse(response);
                        $('#txt_descripcion').html(info.Descripcion);
                        $('#txt_existencia').html(info.Cantidad);
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html(info.Precio_Unitario);
                        $('#txt_precio_total').html(info.Precio_Unitario);

                        //Activar Cantidad
                        $('#txt_cant_producto').removeAttr('disabled');

                        //Mostrar botón agregar
                        $('#add_product_venta').slideDown();
                    } else {
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');

                        //Bloquear Cantidad
                        $('#txt_cant_producto').attr('disabled','disabled');

                        //Ocultar botón agregar
                        $('#add_product_venta').slideUp();
                    }
                },
                error: function(error){
    
                }
            });
        }
    });

    //Validar Cantidad del artículo antes de agregar
    $('#txt_cant_producto').keyup(function(e){
        e.preventDefault();
        var precio_total = $(this).val() * $('#txt_precio').html();
        var existencia = parseInt($('#txt_existencia').html());
        $('#txt_precio_total').html(precio_total);

        //Ocultar el boton agregar si la cantidad es menor que 1
        if( ($(this).val() <= 0 || isNaN($(this).val())) || ($(this).val() > existencia)){
            $('#add_product_venta').slideUp();
        } else {
            $('#add_product_venta').slideDown();
        }
    });

    //Agregar productos al detalle
    $('#add_product_venta').click(function(e){
        e.preventDefault();

        if($('#txt_cant_producto').val() > 0){
            var codproducto = $('#txt_cod_producto').val();
            var cantidad    = $('#txt_cant_producto').val();
            var action = 'addProductDetalle';

            $.ajax({
                url:'ajax.php',
                type:"POST",
                async: true,
                data: {action:action,producto:codproducto,cantidad:cantidad},

                success: function(response){
                    if (response != 'error'){
                        var info = JSON.parse(response);
                        $('#detalle_venta').html(info.detalle);
                        $('#detalle_totales').html(info.totales);

                        $('#txt_cod_producto').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_existencia').html('-');
                        $('#txt_cant_producto').val('0');
                        $('#txt_precio').html('0.00');
                        $('#txt_precio_total').html('0.00');

                        //Bloquear Cantidad
                        $('#txt_cant_producto').attr('disabled','disabled');

                        //Ocultar boton agregar
                        $('#add_product_venta').slideUp();

                    } else {
                        console.log('no data');
                    }
                    viewProcesar();
                },
                error: function(error){

                }
            });
        }
    });

    //Anular Venta
    $('#btn_anular_venta').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta tr').length;
        if(rows > 0){
            var action = 'anularVenta';

            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action},

                success: function(response){
                    if(response != 'error'){
                        location.reload();
                    }
                },
                error: function(error){

                }
            })
        }
    });

    //Facturar Venta
    $('#btn_facturar_venta').click(function(e){
        e.preventDefault();

        var rows = $('#detalle_venta tr').length;
        if(rows > 0){
            var action = 'procesarVenta';
            var codcliente = $('#idcliente').val();

            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action,codcliente:codcliente},

                success: function(response){
                    if(response != 'error'){
                        var info = JSON.parse(response);
                        //console.log(info);
                        generarPDF(info.Cod_Socio,info.IdVenta);
                        location.reload();
                    } else {
                        console.log('no data');
                    }
                },
                error: function(error){
                }
            });
        }
    });

    //Cambiar contraseña
    $('.newPass').keyup(function(){
        validPass();
    });

    //Form Cambiar contraseña
    $('#frmChangePass').submit(function(e){
        e.preventDefault();

        var passActual = $('#txtPassUser').val();
        var passNuevo = $('#txtNewPassUser').val();
        var confirmPassNuevo = $('#txtPassConfirm').val();
        var action = "changePassword";

        if(passNuevo != confirmPassNuevo){
            $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales. </p>');
            $('.alertChangePass').slideDown();
            return false;
        }
    
        if(passNuevo.length < 5){
            $('.alertChangePass').html('<p style="color:red;">La nueva contraseña debe ser de 5 caracteres como mínimo. </p>');
            $('.alertChangePass').slideDown();
            return false;
        }

        $.ajax({
            url : 'ajax.php',
            type: "POST",
            async : true,
            data: {action:action,passActual:passActual,passNuevo:passNuevo},

            success: function(response){
                if(response != 'error'){
                    var info = JSON.parse(response);
                    if(info.cod == '00'){
                        $('.alertChangePass').html('<p style="color:green;">'+info.msg+'</p>');
                        $('#frmChangePass')[0].reset();
                    } else {
                        $('.alertChangePass').html('<p style="color:red;">'+info.msg+'</p>');
                    }
                    $('.alertChangePass').slideDown();
                }
            },
            error: function(error){
            }
        });
    });

    //Ver Factura
    $('.view_factura').click(function(e) {
        e.preventDefault();
        var codCliente = $(this).attr('cl');
        var noFactura = $(this).attr('f');
        generarPDF(codCliente,noFactura);
    })

});

function validPass(){
    var passNuevo = $('#txtNewPassUser').val();
    var confirmPassNuevo = $('#txtPassConfirm').val();
    if(passNuevo != confirmPassNuevo){
        $('.alertChangePass').html('<p style="color:red;">Las contraseñas no son iguales. </p>');
        $('.alertChangePass').slideDown();
        return false;
    }

    if(passNuevo.length < 5){
        $('.alertChangePass').html('<p style="color:red;">La nueva contraseña debe ser de 5 caracteres como mínimo. </p>');
        $('.alertChangePass').slideDown();
        return false;
    }
    $('.alertChangePass').html('');
    $('.alertChangePass').slideUp();
}

function generarPDF(cliente,factura){
    var ancho = 1000;
    var alto = 800;
    //Calcular posicion x,y para centrar la ventana
    var x = parseInt((window.screen.width/2) - (ancho / 2));
    var y = parseInt((window.screen.height/2) - (alto / 2));

    $url = 'generarFactura.php?cl='+cliente+'&f='+factura;
    window.open($url,"Factura","left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=no");
}

function del_product_detalle(correlativo){
    var action = 'delProductDetalle';
    var id_detalle = correlativo;

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function(response){
            if(response != 'error'){
                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);

                $('#txt_cod_producto').val('');
                $('#txt_descripcion').html('-');
                $('#txt_existencia').html('-');
                $('#txt_cant_producto').val('0');
                $('#txt_precio').html('0.00');
                $('#txt_precio_total').html('0.00');

                //Bloquear Cantidad
                $('#txt_cant_product').attr('disabled','disabled');

                //Ocultar boton agregar
                $('#add_product_venta').slideUp();
            } else {
                $('#detalle_venta').html('');
                $('#detalle_totales').html('');
            }
            viewProcesar();
        },
        error: function(error){

        }
    })
}

function viewProcesar(){
    if($('#detalle_venta tr').length > 0){
        $('#btn_facturar_venta').show();
    } else {
        $('#btn_facturar_venta').hide();
    }
}

function searchForDetalle(id){
    var action = 'searchForDetalle';
    var user = id;

    $.ajax({
        url: 'ajax.php',
        type: "POST",
        async: true,
        data: {action:action,user:user},

        success: function(response){
            if(response != 'error'){
                var info = JSON.parse(response);
                $('#detalle_venta').html(info.detalle);
                $('#detalle_totales').html(info.totales);
            } else {
                // console.log('no data');
            }
            viewProcesar();
        },
        error: function(error){

        }
    })
}

function getUrl(){
    var loc = window.location;
    var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
    return loc.href.substring(0, loc.href.lenght - ((loc.pathname + loc.search + loc.hash).lenght - pathName.lenght));
}