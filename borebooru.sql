-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-12-2018 a las 13:50:45
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 5.6.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `borebooru`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `ID` text COLLATE utf8_spanish_ci NOT NULL,
  `TAG` text COLLATE utf8_spanish_ci NOT NULL,
  `FORMAT` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`ID`, `TAG`, `FORMAT`) VALUES
('000000', 'cute girl IRL Rainbow_Dash cosplay', 'jpg'),
('000001', 'cute loli anime screencap', 'png'),
('000002', 'old_man russian phone vintage IRL', 'jpg'),
('000003', 'artist:Devil-Vox cute cat feline male furry maid', 'png'),
('000004', 'artist:Devil-Vox cute cat feline male furry', 'png'),
('000005', 'anime lucy_star Conata screencap', 'jpg'),
('000006', 'screencap Hamilton Laffayette', 'jpg'),
('000007', 'Amy_Rose series:Sonic_The_Hedgehog Sonic_The_Hedgehog_Riders cute', 'jpg'),
('000008', 'video IRL dog ears floppy_ears cute', 'mp4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `numero`
--

CREATE TABLE `numero` (
  `ultimo` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `numero`
--

INSERT INTO `numero` (`ultimo`) VALUES
('000009');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags`
--

CREATE TABLE `tags` (
  `TAG` text COLLATE utf8_spanish_ci NOT NULL,
  `USOS` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tags`
--

INSERT INTO `tags` (`TAG`, `USOS`) VALUES
('girl', '1'),
('Rainbow_Dash', '1'),
('cosplay', '1'),
('loli', '1'),
('old_man', '1'),
('russian', '1'),
('phone', '1'),
('vintage', '1'),
('maid', '1'),
('artist:Devil-Vox', '000002'),
('cat', '000002'),
('feline', '000002'),
('male', '000002'),
('furry', '000002'),
('anime', '000002'),
('lucy_star', '1'),
('Conata', '1'),
('screencap', '000003'),
('Hamilton', '1'),
('Laffayette', '1'),
('Amy_Rose', '1'),
('series:Sonic_The_Hedgehog', '1'),
('Sonic_The_Hedgehog_Riders', '1'),
('video', '1'),
('IRL', '000003'),
('dog', '1'),
('ears', '1'),
('floppy_ears', '1'),
('cute', '000006');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
