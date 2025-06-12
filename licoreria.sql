-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2025 a las 15:49:05
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
-- Base de datos: `licoreria`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarEntradaProducto` (IN `p_id_producto` INT, IN `p_cantidad` INT, IN `p_precio_compra` DECIMAL(10,2), IN `p_cedula_proveedor` VARCHAR(15), IN `p_id_usuario` INT, IN `p_observaciones` VARCHAR(255))   BEGIN
    DECLARE v_existe_relacion INT;
    
    -- Verificar si existe relación proveedor-producto
    SELECT COUNT(*) INTO v_existe_relacion
    FROM proveedor_producto
    WHERE cedula_proveedor = p_cedula_proveedor AND id_producto = p_id_producto;
    
    -- Actualizar o insertar relación proveedor-producto
    IF v_existe_relacion > 0 THEN
        UPDATE proveedor_producto
        SET precio_compra = p_precio_compra,
            fecha_actualizacion = NOW()
        WHERE cedula_proveedor = p_cedula_proveedor AND id_producto = p_id_producto;
    ELSE
        INSERT INTO proveedor_producto (cedula_proveedor, id_producto, precio_compra)
        VALUES (p_cedula_proveedor, p_id_producto, p_precio_compra);
    END IF;
    
    -- Actualizar stock del producto
    UPDATE producto
    SET cantidad = cantidad + p_cantidad
    WHERE id = p_id_producto;
    
    -- Registrar movimiento de inventario
    INSERT INTO movimientos_inventario (
        id_producto, 
        tipo_movimiento, 
        cantidad, 
        precio_unitario, 
        id_usuario, 
        observaciones
    ) VALUES (
        p_id_producto, 
        'ENTRADA', 
        p_cantidad, 
        p_precio_compra, 
        p_id_usuario, 
        p_observaciones
    );
    
    SELECT 'Entrada de producto registrada exitosamente.' AS Mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegistrarVenta` (IN `p_cedula_cliente` VARCHAR(15), IN `p_id_usuario` INT, IN `p_id_producto` INT, IN `p_cantidad` INT)   BEGIN
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_monto_total DECIMAL(10,2);
    DECLARE v_stock_actual INT;
    DECLARE v_id_venta INT;

    -- Obtener el precio del producto
    SELECT precio INTO v_precio
    FROM producto
    WHERE id = p_id_producto;

    -- Calcular el monto total de la venta
    SET v_monto_total = v_precio * p_cantidad;

    -- Verificar si hay suficiente stock
    SELECT cantidad INTO v_stock_actual
    FROM producto
    WHERE id = p_id_producto;

    IF v_stock_actual >= p_cantidad THEN
        -- Insertar la venta en la tabla ventas
        INSERT INTO ventas (cedula_cliente, id_usuario, fecha, monto_total)
        VALUES (p_cedula_cliente, p_id_usuario, CURDATE(), v_monto_total);
        
        SET v_id_venta = LAST_INSERT_ID();

        -- Insertar el detalle de la venta
        INSERT INTO detalle_venta (id_venta, id_producto, cantidad, monto)
        VALUES (v_id_venta, p_id_producto, p_cantidad, v_monto_total);

        -- Registrar movimiento de inventario
        INSERT INTO movimientos_inventario (
            id_producto, 
            tipo_movimiento, 
            cantidad, 
            precio_unitario, 
            id_referencia, 
            tipo_referencia, 
            id_usuario
        ) VALUES (
            p_id_producto, 
            'SALIDA', 
            p_cantidad, 
            v_precio, 
            v_id_venta, 
            'VENTA', 
            p_id_usuario
        );

        SELECT 'Venta registrada exitosamente.' AS Mensaje;
    ELSE
        SELECT 'No hay suficiente stock para realizar la venta.' AS Mensaje;
    END IF;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalcularTotalVentasCliente` (`p_cedula_cliente` VARCHAR(15)) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE v_total DECIMAL(10,2);

    -- Calcular el total de ventas del cliente
    SELECT SUM(monto_total) INTO v_total
    FROM ventas
    WHERE cedula_cliente = p_cedula_cliente;

    -- Si no hay ventas, retornar 0
    IF v_total IS NULL THEN
        SET v_total = 0;
    END IF;

    RETURN v_total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `id_tipo_categoria` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `id_tipo_categoria`, `id_estatus`) VALUES
(1, 'Refrescos', 1, 1),
(2, 'Cervezas', 1, 1),
(3, 'Papas', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `cedula` varchar(15) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `id_estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`cedula`, `id_simbolo_cedula`, `nombres`, `apellidos`, `telefono`, `direccion`, `id_estatus`) VALUES
('V-30330301', 1, 'Samuel', 'Vizamon', '04244354455', 'Valencia', 1),
('V-31117834', 1, 'Moises', 'Vizamon', '04125030111', 'naguanagua', 1),
('V-32670780', 1, 'adoni', 'vespo', '045558974', 'tocuyito', 1),
('V-8609665', 1, 'Zuleima', 'Vizamon', '0412123456', 'av bolivar', 1),
('V-8609666', 1, 'Adolfo', 'Vizamon', '0412336558', 'San Diego', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `cedula_proveedor` varchar(15) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `estado` enum('PENDIENTE','PAGADA','CANCELADA') NOT NULL DEFAULT 'PENDIENTE',
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(255) NOT NULL,
  `id_venta` int(255) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `cantidad` int(10) NOT NULL,
  `monto` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus`
--

CREATE TABLE `estatus` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `estatus`
--

INSERT INTO `estatus` (`id`, `nombre`) VALUES
(1, 'Activo'),
(2, 'Inactivo'),
(3, 'Disponible'),
(4, 'No disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `tipo_movimiento` enum('ENTRADA','SALIDA','AJUSTE') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL COMMENT 'ID de la tabla referencia (venta, compra, etc)',
  `tipo_referencia` varchar(50) DEFAULT NULL COMMENT 'Tipo de referencia (VENTA, COMPRA, etc)',
  `fecha_movimiento` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(10) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perdidas`
--

CREATE TABLE `perdidas` (
  `id` int(11) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(10) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `descripcion`, `cantidad`, `precio`, `id_categoria`, `id_estatus`) VALUES
(1, 'Coca-Cola 2L', 50, 5.00, 1, 1),
(2, 'Pepsi 2L', 40, 4.50, 1, 1),
(3, 'Mani Jacks', 30, 5.00, 2, 1),
(13, 'sevillana', 23, 5.00, 3, 1);

--
-- Disparadores `producto`
--
DELIMITER $$
CREATE TRIGGER `before_producto_delete` BEFORE DELETE ON `producto` FOR EACH ROW BEGIN
    INSERT INTO perdidas (id_producto, cantidad, descripcion, fecha_hora)
    VALUES (OLD.id, OLD.cantidad, 'Producto eliminado', NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `cedula` varchar(15) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombre` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `id_estatus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`cedula`, `id_simbolo_cedula`, `nombre`, `telefono`, `direccion`, `id_estatus`) VALUES
('11223344', 1, 'Bebidas y Licores Premium', '04261234567', 'Centro Comercial Metrópolis, Local 12', 1),
('12345678', 2, 'Distribuidora de Snacks Rápidos', '04121234567', 'Urbanización La Floresta, Calle 2', 1),
('123456789', 4, 'Distribuidora de Bebidas Nacionales C.A.', '04141234567', 'Av. Principal, Zona Industrial, Valencia', 1),
('98765432', 1, 'Licores del Caribe', '04241234567', 'Calle Comercio #123, Naguanagua', 1),
('987654321', 4, 'Importadora de Vinos Internacionales S.A.', '04161234567', 'Av. Bolívar, Edificio Torreón, Piso 5', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_producto`
--

CREATE TABLE `proveedor_producto` (
  `id` int(11) NOT NULL,
  `cedula_proveedor` varchar(15) NOT NULL,
  `id_producto` int(10) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `simbolos_cedula`
--

CREATE TABLE `simbolos_cedula` (
  `id` int(11) NOT NULL,
  `nombre` varchar(2) NOT NULL COMMENT 'Símbolo (V, E, G, J)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `simbolos_cedula`
--

INSERT INTO `simbolos_cedula` (`id`, `nombre`) VALUES
(1, 'V'),
(2, 'E'),
(3, 'G'),
(4, 'J');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_categoria`
--

CREATE TABLE `tipos_categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `tipos_categoria`
--

INSERT INTO `tipos_categoria` (`id`, `nombre`) VALUES
(1, 'Bebidas'),
(2, 'Snacks'),
(3, 'vino'),
(4, 'Ron');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) NOT NULL,
  `cedula` varchar(12) NOT NULL,
  `id_simbolo_cedula` int(11) DEFAULT 1,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `direccion` varchar(255) DEFAULT 'Sin especificar',
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_estatus` int(11) NOT NULL,
  `ultimo_inicio_sesion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `id_simbolo_cedula`, `nombres`, `apellidos`, `telefono`, `direccion`, `user`, `password`, `id_rol`, `id_estatus`, `ultimo_inicio_sesion`) VALUES
(1, '31117854', 1, 'Moises', 'Vizamon', '04125050555', 'Naguanagua', 'admin', '$2y$10$68ac78C.jB5jEQuwNBEvJOy7wEu.3X2UbEKwLtEqBix6LQxnNnSpW', 1, 1, '2025-06-10 09:37:16'),
(2, '30330300', 1, 'Maria', 'Teran', '0414123457', 'Valencia', 'empleado', '$2y$10$InP6m5HokejhvGmvTg9YnuPy14zAy/EB5LKRxv69EndgVdmuf0KMm', 2, 1, '2025-06-08 21:00:21'),
(4, '16579782', 1, 'Angel', 'Perez', '04125030223', 'Valencia', 'Angel123', '$2y$10$l8ILNvg5AQAUliKoN87M8exnKrXvOecoAqMZR/OJKH3L0oGgHXhjW', 2, 1, NULL);

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `after_usuarios_update_ultimo_inicio` BEFORE UPDATE ON `usuarios` FOR EACH ROW BEGIN
    -- Si el campo 'ultimo_inicio_sesion' está siendo actualizado
    IF NEW.ultimo_inicio_sesion IS NOT NULL THEN
        SET NEW.ultimo_inicio_sesion = NOW();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(255) NOT NULL,
  `cedula_cliente` varchar(15) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_detalle_ventas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_detalle_ventas` (
`id_detalle` int(255)
,`id_venta` int(255)
,`cedula_cliente` varchar(15)
,`nombre_usuario` varchar(255)
,`fecha` date
,`id_producto` int(10)
,`producto` varchar(255)
,`cantidad` int(10)
,`monto` decimal(10,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_producto_mas_vendido_mes`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_producto_mas_vendido_mes` (
`Periodo` varchar(7)
,`Producto` varchar(255)
,`Total_Vendido` decimal(32,0)
,`Semana` int(2)
,`Año` int(4)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_producto_menos_vendido`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_producto_menos_vendido` (
`Periodo` varchar(6)
,`Producto` varchar(255)
,`Total_Vendido` decimal(32,0)
,`Semana` int(2)
,`Año` int(4)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_detalle_ventas`
--
DROP TABLE IF EXISTS `vista_detalle_ventas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_detalle_ventas`  AS SELECT `dv`.`id` AS `id_detalle`, `v`.`id` AS `id_venta`, `v`.`cedula_cliente` AS `cedula_cliente`, `u`.`nombres` AS `nombre_usuario`, `v`.`fecha` AS `fecha`, `dv`.`id_producto` AS `id_producto`, `p`.`descripcion` AS `producto`, `dv`.`cantidad` AS `cantidad`, `dv`.`monto` AS `monto` FROM (((`detalle_venta` `dv` join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) join `usuarios` `u` on(`v`.`id_usuario` = `u`.`id`)) join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_producto_mas_vendido_mes`
--
DROP TABLE IF EXISTS `vista_producto_mas_vendido_mes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_producto_mas_vendido_mes`  AS SELECT date_format(`v`.`fecha`,'%Y-%m') AS `Periodo`, `p`.`descripcion` AS `Producto`, sum(`dv`.`cantidad`) AS `Total_Vendido`, week(`v`.`fecha`) AS `Semana`, year(`v`.`fecha`) AS `Año` FROM ((`detalle_venta` `dv` join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) GROUP BY date_format(`v`.`fecha`,'%Y-%m'), `p`.`descripcion`, week(`v`.`fecha`), year(`v`.`fecha`) ORDER BY sum(`dv`.`cantidad`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_producto_menos_vendido`
--
DROP TABLE IF EXISTS `vista_producto_menos_vendido`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_producto_menos_vendido`  AS SELECT 'Semana' AS `Periodo`, `p`.`descripcion` AS `Producto`, sum(`dv`.`cantidad`) AS `Total_Vendido`, week(`v`.`fecha`) AS `Semana`, year(`v`.`fecha`) AS `Año` FROM ((`detalle_venta` `dv` join `producto` `p` on(`dv`.`id_producto` = `p`.`id`)) join `ventas` `v` on(`dv`.`id_venta` = `v`.`id`)) GROUP BY `p`.`id`, week(`v`.`fecha`), year(`v`.`fecha`) ORDER BY sum(`dv`.`cantidad`) ASC LIMIT 0, 1 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo_categoria` (`id_tipo_categoria`),
  ADD KEY `id_estatus` (`id_estatus`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_estatus` (`id_estatus`),
  ADD KEY `clientes_ibfk_simbolo` (`id_simbolo_cedula`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cedula_proveedor` (`cedula_proveedor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_compra` (`id_compra`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `estatus`
--
ALTER TABLE `estatus`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `perdidas`
--
ALTER TABLE `perdidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_estatus` (`id_estatus`),
  ADD KEY `idx_producto_descripcion` (`descripcion`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_estatus` (`id_estatus`),
  ADD KEY `proveedores_ibfk_simbolo` (`id_simbolo_cedula`);

--
-- Indices de la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `proveedor_producto_unico` (`cedula_proveedor`,`id_producto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `simbolos_cedula`
--
ALTER TABLE `simbolos_cedula`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipos_categoria`
--
ALTER TABLE `tipos_categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_unico` (`user`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_estatus` (`id_estatus`),
  ADD KEY `usuarios_ibfk_simbolo` (`id_simbolo_cedula`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cedula_cliente` (`cedula_cliente`),
  ADD KEY `ventas_ibfk_usuario` (`id_usuario`),
  ADD KEY `idx_ventas_fecha` (`fecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estatus`
--
ALTER TABLE `estatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `perdidas`
--
ALTER TABLE `perdidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `simbolos_cedula`
--
ALTER TABLE `simbolos_cedula`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipos_categoria`
--
ALTER TABLE `tipos_categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`id_tipo_categoria`) REFERENCES `tipos_categoria` (`id`),
  ADD CONSTRAINT `categorias_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`);

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  ADD CONSTRAINT `clientes_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`);

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`cedula_proveedor`) REFERENCES `proveedores` (`cedula`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compras` (`id`),
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `perdidas`
--
ALTER TABLE `perdidas`
  ADD CONSTRAINT `perdidas_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`);

--
-- Filtros para la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  ADD CONSTRAINT `proveedores_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`);

--
-- Filtros para la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  ADD CONSTRAINT `proveedor_producto_ibfk_1` FOREIGN KEY (`cedula_proveedor`) REFERENCES `proveedores` (`cedula`),
  ADD CONSTRAINT `proveedor_producto_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_estatus`) REFERENCES `estatus` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_simbolo` FOREIGN KEY (`id_simbolo_cedula`) REFERENCES `simbolos_cedula` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cedula_cliente`) REFERENCES `clientes` (`cedula`),
  ADD CONSTRAINT `ventas_ibfk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
