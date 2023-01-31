-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 28, 2023 alle 02:40
-- Versione del server: 10.4.27-MariaDB
-- Versione PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voli`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `voloprenotato`
--

CREATE TABLE `voloprenotato` (
  `IDVolo` int(11) NOT NULL,
  `IDPersona` varchar(20) NOT NULL,
  `partenza` varchar(50) NOT NULL,
  `arrivo` varchar(100) NOT NULL,
  `Terminal` varchar(5) NOT NULL,
  `Data` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `voloprenotato`
--

INSERT INTO `voloprenotato` (`IDVolo`, `IDPersona`, `partenza`, `arrivo`, `Terminal`, `Data`) VALUES
(800, '5335748327', 'Tontouta', 'Narita International Airport', 'null', '2023-01-26'),
(1420, '5335748327', 'Canberra', 'Sydney Kingsford Smith Airport', 'null', '2023-01-26'),
(4587, '5335748327', 'Melbourne - Tullamarine Airport', 'Sydney Kingsford Smith Airport', '1', '2023-01-26'),
(6559, '5335748327', 'Christchurch International', 'Sydney Kingsford Smith Airport', 'null', '2023-01-26'),
(6805, '5335748327', 'Shanghai Pudong International', 'Shuangliu', 'null', '2023-01-26'),
(7687, '5335748327', 'Perth International', 'Singapore Changi', '1', '2023-01-26');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `voloprenotato`
--
ALTER TABLE `voloprenotato`
  ADD PRIMARY KEY (`IDVolo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
