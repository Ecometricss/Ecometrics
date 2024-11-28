-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-11-2024 a las 18:58:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecometric2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cultivos`
--

CREATE TABLE `cultivos` (
  `ID_Cultivo` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Temperatura_minima` float DEFAULT NULL,
  `Temperatura_maxima` float DEFAULT NULL,
  `Humedad_minima` float NOT NULL,
  `Humedad_maxima` float NOT NULL,
  `Temperatura_ambiente` float NOT NULL,
  `Max_temp_amb` float NOT NULL,
  `Humedad_ambiente` float NOT NULL,
  `Max_hum_amb` float NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cultivos`
--

INSERT INTO `cultivos` (`ID_Cultivo`, `Nombre`, `Temperatura_minima`, `Temperatura_maxima`, `Humedad_minima`, `Humedad_maxima`, `Temperatura_ambiente`, `Max_temp_amb`, `Humedad_ambiente`, `Max_hum_amb`, `ID_Usuario`) VALUES
(22, 'Zanahoria', NULL, NULL, 12, 12, 12, 12, 12, 12, 29);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_monitoreo`
--

CREATE TABLE `datos_monitoreo` (
  `ID_Monitoreo` int(11) NOT NULL,
  `ID_Invernadero` int(11) DEFAULT NULL,
  `Temperatura` float DEFAULT NULL,
  `Humedad` float DEFAULT NULL,
  `Temperatura_amb` float NOT NULL,
  `Humedad_amb` float NOT NULL,
  `Fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invernaderos`
--

CREATE TABLE `invernaderos` (
  `ID_Invernadero` int(11) NOT NULL,
  `ID_Usuario` int(11) DEFAULT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Ubicacion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `invernaderos`
--

INSERT INTO `invernaderos` (`ID_Invernadero`, `ID_Usuario`, `Nombre`, `Ubicacion`) VALUES
(48, 29, 'Terreno14', 'Boyaca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `Contraseña` varchar(255) NOT NULL,
  `Fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Nombre`, `Correo`, `Contraseña`, `Fecha_creacion`) VALUES
(28, 'Johan', 'johan12@gmail.com', '$2y$10$mPmSHw/TIIHSkWgBEX1xpuNH4sohEychpwrEbnQhmyzzrtBx2n25C', '2024-11-18 19:22:38'),
(29, 'Johan', 'laura@gmail.com', '$2y$10$qQllEU0iJPMDAQWb/ZS9uubLbfk1ZKBk9augACs4XqJihCbzrAmAW', '2024-11-20 16:34:26');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cultivos`
--
ALTER TABLE `cultivos`
  ADD PRIMARY KEY (`ID_Cultivo`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `datos_monitoreo`
--
ALTER TABLE `datos_monitoreo`
  ADD PRIMARY KEY (`ID_Monitoreo`),
  ADD KEY `ID_Invernadero` (`ID_Invernadero`);

--
-- Indices de la tabla `invernaderos`
--
ALTER TABLE `invernaderos`
  ADD PRIMARY KEY (`ID_Invernadero`),
  ADD KEY `ID_Usuario` (`ID_Usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cultivos`
--
ALTER TABLE `cultivos`
  MODIFY `ID_Cultivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `datos_monitoreo`
--
ALTER TABLE `datos_monitoreo`
  MODIFY `ID_Monitoreo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `invernaderos`
--
ALTER TABLE `invernaderos`
  MODIFY `ID_Invernadero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cultivos`
--
ALTER TABLE `cultivos`
  ADD CONSTRAINT `cultivos_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`);

--
-- Filtros para la tabla `datos_monitoreo`
--
ALTER TABLE `datos_monitoreo`
  ADD CONSTRAINT `datos_monitoreo_ibfk_1` FOREIGN KEY (`ID_Invernadero`) REFERENCES `invernaderos` (`ID_Invernadero`);

--
-- Filtros para la tabla `invernaderos`
--
ALTER TABLE `invernaderos`
  ADD CONSTRAINT `invernaderos_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
