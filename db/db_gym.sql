-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2023 a las 20:02:04
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_gym`
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
  `Descripcion` varchar(50) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(8,2) NOT NULL,
  `Marca` varchar(20) NOT NULL,
  `Cod_Proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `articulos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `IdCaja` int(11) NOT NULL,
  `FechaApertura` datetime NOT NULL DEFAULT current_timestamp(),
  `Actividad` text NOT NULL,
  `Monto_inicial` decimal(8,2) NOT NULL,
  `Monto_salida` decimal(8,2) NOT NULL,
  `Total_caja` decimal(8,2) NOT NULL,
  `Cod_Empleado` int(11) NOT NULL,
  `Estado` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caja`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clases`
--

CREATE TABLE `clases` (
  `IdClase` int(11) NOT NULL,
  `NombreC` varchar(50) NOT NULL,
  `Duracion` varchar(50) DEFAULT NULL,
  `Costo_Clase` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clases`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta_servicios`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `IdEmpleado` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Dni` int(11) NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Usuario` varchar(20) NOT NULL,
  `Clave` varchar(50) NOT NULL,
  `Rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`IdEmpleado`, `Nombre`, `Dni`, `Direccion`, `Telefono`, `Email`, `Usuario`, `Clave`, `Rol`) VALUES
(1, 'Pablo Ruiz', 35900000, 'Calle x 210', 155115555, 'pabloruizok@mail.com', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(2, 'Maria Diaz', 38000100, 'Calle y 190', 152000090, 'mariadiaz1@hmail.com', 'user1', 'ee11cbb19052e40b07aac0ca060c23ee', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instructores`
--

CREATE TABLE `instructores` (
  `Id_Instructor` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Dni` int(10) UNSIGNED NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Sueldo` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instructores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `IdProveedor` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Codigo_Postal` int(11) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `IdRol` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL
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
  `Nombre` varchar(50) NOT NULL,
  `Dni` int(11) NOT NULL,
  `Direccion` varchar(50) NOT NULL,
  `Telefono` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `Id_Clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Volcado de datos para la tabla `ventas`
--

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
  ADD PRIMARY KEY (`IdClase`);

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
  ADD PRIMARY KEY (`Id_Socio`),
  ADD KEY `fk_relacion` (`Id_Clase`);

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
  MODIFY `IdArticulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `caja`
--
ALTER TABLE `caja`
  MODIFY `IdCaja` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT de la tabla `clases`
--
ALTER TABLE `clases`
  MODIFY `IdClase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `detallefactura`
--
ALTER TABLE `detallefactura`
  MODIFY `IdDetalle_venta_art` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `correlativo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT de la tabla `detalle_venta_servicios`
--
ALTER TABLE `detalle_venta_servicios`
  MODIFY `IdDetalle_venta_serv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `IdEmpleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `instructores`
--
ALTER TABLE `instructores`
  MODIFY `Id_Instructor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `IdProveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `IdRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `socios`
--
ALTER TABLE `socios`
  MODIFY `Id_Socio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `IdVenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

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

--
-- Filtros para la tabla `socios`
--
ALTER TABLE `socios`
  ADD CONSTRAINT `fk_relacion` FOREIGN KEY (`Id_Clase`) REFERENCES `clases` (`IdClase`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
