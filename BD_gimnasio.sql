-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gimnasio`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (IN `codigo` INT, IN `cantidad` INT)   BEGIN
            DECLARE precio_actual decimal(10,2);
            SELECT Precio_Unitario INTO precio_actual FROM articulos WHERE IdArticulo = codigo;
            
            INSERT INTO detalle_temp (codArticulo,cantidad,precio_venta) VALUES (codigo,cantidad,precio_actual);
            
            SELECT tmp.correlativo, tmp.codArticulo, a.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER JOIN articulos a
            ON tmp.codArticulo = a.IdArticulo;
            
       END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()   BEGIN
    
          DECLARE usuarios int;
          DECLARE socios int;
          DECLARE proveedores int;
          DECLARE articulos int;
          DECLARE ventas int;
          
          SELECT COUNT(*) INTO usuarios FROM empleados;
          SELECT COUNT(*) INTO socios FROM socios;
          SELECT COUNT(*) INTO proveedores FROM proveedores;
          SELECT COUNT(*) INTO articulos FROM articulos;
          SELECT COUNT(*) INTO ventas FROM ventas WHERE Fecha > CURDATE();
          
          SELECT usuarios,socios,proveedores,articulos,ventas;
          
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (IN `id_detalle` INT)   BEGIN

           DELETE FROM detalle_temp WHERE correlativo = id_detalle;

           SELECT tmp.correlativo, tmp.codArticulo, a.descripcion, tmp.cantidad, tmp.precio_venta FROM detalle_temp tmp INNER JOIN articulos a ON tmp.codArticulo = a.IdArticulo;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT)   BEGIN
         DECLARE factura INT;
         DECLARE registros INT;
         DECLARE total DECIMAL(8,2);
         
         DECLARE nueva_existencia int;
         DECLARE existencia_actual int;

         DECLARE incaja INT;
         DECLARE totalcaj DECIMAL(8,2);
         
         DECLARE tmp_cod_producto int;
         DECLARE tmp_cant_producto int;
         DECLARE a INT;
         SET a = 1;
         
         CREATE TEMPORARY TABLE tbl_tmp_tokenuser (
                                                   id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                                   cod_prod BIGINT,
                                                   cant_prod int);

         SET registros = (SELECT COUNT(*) FROM detalle_temp);
         
         IF registros > 0 THEN
                                 INSERT INTO tbl_tmp_tokenuser(cod_prod,cant_prod) SELECT codArticulo,cantidad FROM detalle_temp;
            
                                 INSERT INTO ventas (Cod_Socio) VALUES (cod_cliente);
                                 SET factura = LAST_INSERT_ID();
            
            INSERT INTO detallefactura (nroFactura,CodArticulo,Cantidad,precio_venta) SELECT (factura) AS nroFactura,codArticulo,cantidad,precio_venta FROM detalle_temp;
            
            WHILE a <= registros DO
                 SELECT cod_prod,cant_prod INTO tmp_cod_producto,tmp_cant_producto FROM tbl_tmp_tokenuser WHERE id = a;
                 
                 SELECT Cantidad INTO existencia_actual FROM articulos WHERE IdArticulo = tmp_cod_producto;
                 SET nueva_existencia = existencia_actual - tmp_cant_producto;
                 UPDATE articulos SET Cantidad = nueva_existencia WHERE IdArticulo = tmp_cod_producto;
                 
                 SET a=a+1;
            END WHILE;
            
                      SET total = (SELECT SUM(cantidad * precio_venta) FROM detalle_temp);
                      SET totalcaj = (SELECT SUM(Total_caja) + total as totalc FROM caja WHERE IdCaja = (SELECT MAX(IdCaja) FROM caja));
                      
                      INSERT INTO caja (Actividad,Monto_inicial,Total_caja,Cod_Empleado,Estado) VALUES ('Venta de Artículo', total, totalcaj, cod_usuario,'Abierto');
	              SET incaja= LAST_INSERT_ID();                                         
               
                      UPDATE ventas SET Total = total, Cod_Caja = incaja WHERE IdVenta = factura;
                      DELETE FROM detalle_temp;
                      TRUNCATE TABLE tbl_tmp_tokenuser;
                      SELECT * FROM ventas WHERE IdVenta = factura;

         ELSE
            SELECT 0;
         END IF;
   END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `IdArticulo` int(11) NOT NULL,
  `Descripcion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(8,2) NOT NULL,
  `Marca` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Cod_Proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`IdArticulo`, `Descripcion`, `Cantidad`, `Precio_Unitario`, `Marca`, `Cod_Proveedor`) VALUES
(1, 'Proteina 500gr', 20, '950.00', 'Promax', 2),
(2, 'Creatina 200gr', 10, '790.00', 'Myprotein', 3),
(3, 'Proteina 1kg', 20, '1000.00', 'MegaMax', 1),
(4, 'Proteina 1kg', 14, '988.00', 'Nitrotech', 1),
(5, 'Glutamina 1.5kg', 50, '1200.00', 'Powder', 2),
(6, 'Energizante 200ml ', 90, '75.00', 'Energy Drink', 1),
(7, 'Proteína vegetal 900gr', 37, '2500.00', 'Freaking Good', 4),
(8, 'Avena integral en polvo 1250gr', 27, '690.00', 'PROZIS', 4),
(9, 'Harina de avena y proteína 100gr', 0, '589.00', 'PROZIS', 4),
(10, 'Agua mineral 500ml', 80, '100.00', 'Villavicencio', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `IdCaja` int(11) NOT NULL,
  `FechaApertura` datetime NOT NULL DEFAULT current_timestamp(),
  `Actividad` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `Monto_inicial` decimal(8,2) NOT NULL,
  `Monto_salida` decimal(8,2) NOT NULL,
  `Total_caja` decimal(8,2) NOT NULL,
  `Cod_Empleado` int(11) NOT NULL,
  `Estado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `IdClase` int(11) NOT NULL,
  `Cod_Instructor` int(11) NOT NULL,
  `NombreC` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dias` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Hora` time NOT NULL,
  `Duracion` time NOT NULL,
  `Costo_Clase` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clases`
--

INSERT INTO `clases` (`IdClase`, `Cod_Instructor`, `NombreC`, `Dias`, `Hora`, `Duracion`, `Costo_Clase`) VALUES
(1, 3, 'Taebo', 'Mar, Jue, Sab', '10:00:00', '01:30:00', '500.00'),
(2, 2, 'Spinning', 'Lun, Mie, Vie', '16:00:00', '01:30:00', '500.00'),
(3, 1, 'Pesas', 'Lun, Mar, Mie, Jue, Vie', '08:00:00', '01:30:00', '200.00'),
(4, 4, 'Zumba', 'Lun, Mie, Vie', '18:00:00', '01:30:00', '500.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallefactura`
--

CREATE TABLE `detallefactura` (
  `IdDetalle_venta_art` int(11) NOT NULL,
  `nroFactura` int(11) NOT NULL,
  `CodArticulo` int(11) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `precio_venta` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `correlativo` int(11) NOT NULL,
  `nroFactura` int(11) NOT NULL,
  `codArticulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta_servicios`
--

CREATE TABLE `detalle_venta_servicios` (
  `IdDetalle_venta_serv` int(11) NOT NULL,
  `Cod_Venta` int(11) NOT NULL,
  `Cod_Clase` int(11) NOT NULL,
  `Periodo` varchar(25) NOT NULL,
  `Fecha_Alta` date NOT NULL,
  `Fecha_Vencim` date NOT NULL,
  `Total` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `IdEmpleado` int(11) NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dni` int(11) NOT NULL,
  `Direccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Usuario` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Clave` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`IdEmpleado`, `Nombre`, `Dni`, `Direccion`, `Telefono`, `Email`, `Usuario`, `Clave`, `Rol`) VALUES
(1, 'Pablo Ruiz', 35900000, 'Calle x 2100', 155115511, 'pabloruizok@mail.com', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(2, 'Marcos Gimenez', 38000100, 'Madrid 190', 150000090, 'marcgimenez2@hmail.com', 'user1', 'ee11cbb19052e40b07aac0ca060c23ee', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructores`
--

CREATE TABLE `instructores` (
  `Id_Instructor` int(11) NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dni` int(10) UNSIGNED NOT NULL,
  `Direccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Sueldo` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instructores`
--

INSERT INTO `instructores` (`Id_Instructor`, `Nombre`, `Dni`, `Direccion`, `Telefono`, `Email`, `Sueldo`) VALUES
(1, 'Esteban Pizarro', 39900999, 'Rivadavia 182', 150066666, 'epizarrook@mail.com', '15000.00'),
(2, 'Jonathan Orozco', 36666676, 'Palmeras 121', 159111110, 'orozcoj@mail.com', '15000.00'),
(3, 'Ariana Rossi', 32121212, 'Union 2393', 154444448, 'ariirossi@hmail.com', '15000.00'),
(4, 'Lucas Moreno', 40888885, 'Casa 134', 156000087, 'lucccc@outlook.com', '15000.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `IdProveedor` int(11) NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Direccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Codigo_Postal` int(11) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`IdProveedor`, `Nombre`, `Direccion`, `Codigo_Postal`, `Telefono`, `Email`) VALUES
(1, 'Profit sa', 'Tucuman 1521', 4500, 157171717, 'contacto@profitsa.com'),
(2, 'Fitx sa', 'Justo 1992', 4500, 4210000, 'fitx1sa@mail.net'),
(3, 'Nutrirte comp', 'Solar 1221', 4500, 159090909, 'nutrirteproduct@mail.com'),
(4, 'PROZIS srl', 'España 1249', 4500, 159000109, 'espprozissrl@contacto.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `IdRol` int(11) NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`IdRol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `socios`
--

CREATE TABLE `socios` (
  `Id_Socio` int(11) NOT NULL,
  `Nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Dni` int(11) NOT NULL,
  `Direccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `socios`
--

INSERT INTO `socios` (`Id_Socio`, `Nombre`, `Dni`, `Direccion`, `Telefono`, `Email`) VALUES
(1, 'Marcos Gutierrez', 30000900, 'Calle x 1880', 159919111, 'mgutierrez@mail.com'),
(2, 'Maria Gomez', 34440444, 'Ungria 100', 159090000, 'mariagomez@mail.com'),
(3, 'Marcos Rios', 38888000, 'Angola 818', 157777777, 'mrios88@hmail.com'),
(4, 'Teresa Meza', 40000001, 'Luna 123', 155050000, 'mteresaa@mail.com'),
(5, 'Julio Santos', 32900001, 'Uruguay 1584', 157000007, 'julsantosok@wmail.com'),

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `IdVenta` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `Cod_Caja` int(11) DEFAULT NULL,
  `Cod_Socio` int(11) DEFAULT NULL,
  `Total` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`IdArticulo`),
  ADD KEY `Cod_Proveedor` (`Cod_Proveedor`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`IdCaja`),
  ADD KEY `Cod_Empleado` (`Cod_Empleado`);

--
-- Indices de la tabla `clases`
--
ALTER TABLE `clases`
  ADD PRIMARY KEY (`IdClase`),
  ADD KEY `Cod_Instructor` (`Cod_Instructor`);

--
-- Indices de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD PRIMARY KEY (`IdDetalle_venta_art`),
  ADD KEY `CodArticulo` (`CodArticulo`),
  ADD KEY `nroFactura` (`nroFactura`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`correlativo`),
  ADD KEY `codArticulo` (`codArticulo`);

--
-- Indices de la tabla `detalle_venta_servicios`
--
ALTER TABLE `detalle_venta_servicios`
  ADD PRIMARY KEY (`IdDetalle_venta_serv`),
  ADD KEY `Cod_Venta` (`Cod_Venta`),
  ADD KEY `Cod_Clase` (`Cod_Clase`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`IdEmpleado`),
  ADD KEY `Rol` (`Rol`);

--
-- Indices de la tabla `instructores`
--
ALTER TABLE `instructores`
  ADD PRIMARY KEY (`Id_Instructor`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`IdProveedor`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`IdRol`);

--
-- Indices de la tabla `socios`
--
ALTER TABLE `socios`
  ADD PRIMARY KEY (`Id_Socio`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`IdVenta`),
  ADD KEY `Cod_Caja` (`Cod_Caja`),
  ADD KEY `Cod_Socio` (`Cod_Socio`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `IdArticulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `IdCaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `IdClase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `IdDetalle_venta_art` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT de la tabla `detalle_venta_servicios`
--
ALTER TABLE `detalle_venta_servicios`
  MODIFY `IdDetalle_venta_serv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `IdEmpleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `instructores`
--
ALTER TABLE `instructores`
  MODIFY `Id_Instructor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `IdProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `IdRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `Id_Socio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `IdVenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  ADD CONSTRAINT `detallefactura_ibfk_1` FOREIGN KEY (`CodArticulo`) REFERENCES `articulos` (`IdArticulo`),
  ADD CONSTRAINT `detallefactura_ibfk_2` FOREIGN KEY (`nroFactura`) REFERENCES `ventas` (`IdVenta`);

--
-- Filtros para la tabla `detalle_venta_servicios`
--
ALTER TABLE `detalle_venta_servicios`
  ADD CONSTRAINT `detalle_venta_servicios_ibfk_1` FOREIGN KEY (`Cod_Venta`) REFERENCES `ventas` (`IdVenta`),
  ADD CONSTRAINT `detalle_venta_servicios_ibfk_2` FOREIGN KEY (`Cod_Clase`) REFERENCES `clases` (`IdClase`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
