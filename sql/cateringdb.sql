-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Gegenereerd op: 26 mei 2025 om 15:56
-- Serverversie: 11.7.2-MariaDB-ubu2404
-- PHP-versie: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cateringdb`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Facility`
--

CREATE TABLE `Facility` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `creation_date` date NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Facility_Tag`
--

CREATE TABLE `Facility_Tag` (
  `tag_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Location`
--

CREATE TABLE `Location` (
  `location_id` int(11) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(12) NOT NULL,
  `country_code` char(2) NOT NULL,
  `phone_number` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `Tag`
--

CREATE TABLE `Tag` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `Facility`
--
ALTER TABLE `Facility`
  ADD PRIMARY KEY (`facility_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexen voor tabel `Facility_Tag`
--
ALTER TABLE `Facility_Tag`
  ADD PRIMARY KEY (`tag_id`,`facility_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexen voor tabel `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexen voor tabel `Tag`
--
ALTER TABLE `Tag`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `Facility`
--
ALTER TABLE `Facility`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Location`
--
ALTER TABLE `Location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `Tag`
--
ALTER TABLE `Tag`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `Facility`
--
ALTER TABLE `Facility`
  ADD CONSTRAINT `location_id` FOREIGN KEY (`location_id`) REFERENCES `Location` (`location_id`);

--
-- Beperkingen voor tabel `Facility_Tag`
--
ALTER TABLE `Facility_Tag`
  ADD CONSTRAINT `Facility_Tag_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `Tag` (`tag_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Facility_Tag_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `Facility` (`facility_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
