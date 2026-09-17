-- ============================================================================
-- Sneakerness Database Script
-- Auteur       : Sneakerness Dev Team
-- Datum        : 2026-09-16
-- Beschrijving : Complete database definitie en testdata voor Sneakerness Rotterdam.
--                Bevat tabellen voor Organisator, Bezoeker, Evenement, Prijs,
--                Ticket, Verkoper, Stand, Contactpersoon en ContactPerVerkoper.
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `sneakerness` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sneakerness`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ContactPerVerkoper`;
DROP TABLE IF EXISTS `Contactpersoon`;
DROP TABLE IF EXISTS `Stand`;
DROP TABLE IF EXISTS `Ticket`;
DROP TABLE IF EXISTS `Prijs`;
DROP TABLE IF EXISTS `Evenement`;
DROP TABLE IF EXISTS `Bezoeker`;
DROP TABLE IF EXISTS `Verkoper`;
DROP TABLE IF EXISTS `Organisator`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- 1. Organisator
-- ----------------------------------------------------------------------------
CREATE TABLE `Organisator` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Naam` VARCHAR(100) NOT NULL,
    `Gebruikersnaam` VARCHAR(100) NOT NULL UNIQUE,
    `Wachtwoord` VARCHAR(255) NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Bezoeker
-- ----------------------------------------------------------------------------
CREATE TABLE `Bezoeker` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Naam` VARCHAR(100) NOT NULL,
    `Email` VARCHAR(100) NOT NULL UNIQUE,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Evenement
-- ----------------------------------------------------------------------------
CREATE TABLE `Evenement` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `OrganisatorId` INT NOT NULL,
    `Naam` VARCHAR(100) NOT NULL,
    `Datum` DATE NOT NULL,
    `Locatie` VARCHAR(150) NOT NULL,
    `AantalTicketsPerTijdslot` INT NOT NULL,
    `BeschikbareStands` INT NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`),
    CONSTRAINT `fk_evenement_organisator` FOREIGN KEY (`OrganisatorId`) REFERENCES `Organisator` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. Prijs
-- ----------------------------------------------------------------------------
CREATE TABLE `Prijs` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `EvenementId` INT NOT NULL,
    `Datum` DATE NOT NULL,
    `Tijdslot` TIME NOT NULL,
    `Tarief` DECIMAL(6,2) NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`),
    CONSTRAINT `fk_prijs_evenement` FOREIGN KEY (`EvenementId`) REFERENCES `Evenement` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. Ticket
-- ----------------------------------------------------------------------------
CREATE TABLE `Ticket` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `BezoekerId` INT NOT NULL,
    `EvenementId` INT NOT NULL,
    `PrijsId` INT NOT NULL,
    `AantalTickets` INT NOT NULL,
    `Datum` DATE NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`),
    CONSTRAINT `fk_ticket_bezoeker` FOREIGN KEY (`BezoekerId`) REFERENCES `Bezoeker` (`Id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ticket_evenement` FOREIGN KEY (`EvenementId`) REFERENCES `Evenement` (`Id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ticket_prijs` FOREIGN KEY (`PrijsId`) REFERENCES `Prijs` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. Verkoper
-- ----------------------------------------------------------------------------
CREATE TABLE `Verkoper` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Naam` VARCHAR(100) NOT NULL,
    `SpecialeStatus` BIT NOT NULL DEFAULT b'0',
    `VerkooptSoort` VARCHAR(100) NOT NULL,
    `Logo` VARCHAR(255) NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. Stand
-- ----------------------------------------------------------------------------
CREATE TABLE `Stand` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `VerkoperId` INT NOT NULL,
    `StandType` VARCHAR(10) NOT NULL,
    `Prijs` DECIMAL(6,2) NOT NULL,
    `AantalDagen` TINYINT NOT NULL,
    `VerhuurdStatus` BIT NOT NULL DEFAULT b'0',
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`),
    CONSTRAINT `fk_stand_verkoper` FOREIGN KEY (`VerkoperId`) REFERENCES `Verkoper` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. Contactpersoon
-- ----------------------------------------------------------------------------
CREATE TABLE `Contactpersoon` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `Naam` VARCHAR(100) NOT NULL,
    `Telefoonnummer` VARCHAR(20) NOT NULL,
    `Email` VARCHAR(100) NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. ContactPerVerkoper
-- ----------------------------------------------------------------------------
CREATE TABLE `ContactPerVerkoper` (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `VerkoperId` INT NOT NULL,
    `ContactpersoonId` INT NOT NULL,
    `IsActief` BIT NOT NULL DEFAULT b'1',
    `Opmerking` VARCHAR(250) NULL,
    `DatumAangemaakt` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    `DatumGewijzigd` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (`Id`),
    CONSTRAINT `fk_cpv_verkoper` FOREIGN KEY (`VerkoperId`) REFERENCES `Verkoper` (`Id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cpv_contactpersoon` FOREIGN KEY (`ContactpersoonId`) REFERENCES `Contactpersoon` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SEED DATA
-- ============================================================================

-- Organisatoren
INSERT INTO `Organisator` (`Id`, `Naam`, `Gebruikersnaam`, `Wachtwoord`, `IsActief`, `Opmerking`) VALUES
(1, 'Sneakerness Events BV', 'sneakerness.events', '$2y$12$Nlm5y7g9r9zP.4r9oB64Iuxc7mRj1H/YeqjO9hUvG.m8dC.Nq8XKi', b'1', 'Hoofdorganisator Sneakerness Rotterdam'),
(2, 'Urban Culture Nederland', 'urban.culture', '$2y$12$Nlm5y7g9r9zP.4r9oB64Iuxc7mRj1H/YeqjO9hUvG.m8dC.Nq8XKi', b'1', 'Co-organisator');

-- Bezoekers
INSERT INTO `Bezoeker` (`Id`, `Naam`, `Email`, `IsActief`, `Opmerking`) VALUES
(1, 'Lisa Jansen', 'lisa.jansen@gmail.com', b'1', 'VIP bezoeker'),
(2, 'Ahmed El Mansouri', 'ahmed.elmansouri@outlook.com', b'1', 'Reguliere bezoeker'),
(3, 'Emma van der Berg', 'emma.vdberg@hotmail.com', b'1', 'Reguliere bezoeker'),
(4, 'Tom Bakker', 'tom.bakker@gmail.com', b'1', 'Sneaker collector');

-- Evenementen
INSERT INTO `Evenement` (`Id`, `OrganisatorId`, `Naam`, `Datum`, `Locatie`, `AantalTicketsPerTijdslot`, `BeschikbareStands`, `IsActief`, `Opmerking`) VALUES
(1, 1, 'Sneakerness Rotterdam 2026', '2026-10-24', 'Van Nelle Fabriek Rotterdam', 750, 55, b'1', 'Sneakerness Rotterdam Drop Hub Editie 2026');

-- Prijzen
INSERT INTO `Prijs` (`Id`, `EvenementId`, `Datum`, `Tijdslot`, `Tarief`, `IsActief`, `Opmerking`) VALUES
(1, 1, '2026-10-24', '10:00:00', 25.00, b'1', 'Early Bird Vroege Toegang'),
(2, 1, '2026-10-24', '12:00:00', 20.00, b'1', 'Middagsessie 1'),
(3, 1, '2026-10-24', '14:30:00', 20.00, b'1', 'Middagsessie 2');

-- Tickets
INSERT INTO `Ticket` (`Id`, `BezoekerId`, `EvenementId`, `PrijsId`, `AantalTickets`, `Datum`, `IsActief`, `Opmerking`) VALUES
(1, 1, 1, 1, 2, '2026-09-01', b'1', 'Vroege toegang tickets'),
(2, 2, 1, 2, 1, '2026-09-02', b'1', 'Middag ticket'),
(3, 3, 1, 3, 4, '2026-09-03', b'1', 'Groepsticket vrienden');

-- ----------------------------------------------------------------------------
-- Verkopers (48 totaal, 8 Partners, stand types 18x AA+, 20x AA, 10x A)
-- ----------------------------------------------------------------------------
INSERT INTO `Verkoper` (`Id`, `Naam`, `SpecialeStatus`, `VerkooptSoort`, `Logo`, `IsActief`, `Opmerking`) VALUES
(1, 'Kicks Rotterdam', b'1', 'Sneakers', 'kicks_rtm.png', b'1', 'Actief'),
(2, 'SoleMate Amsterdam', b'0', 'Sneakers', 'solemate.png', b'1', 'Bevestigd'),
(3, 'Streetwear RTM', b'0', 'Streetwear', 'streetwear_rtm.png', b'1', 'In behandeling'),
(4, 'Sneaker Art Studio', b'1', 'Customizer', 'sneaker_art.png', b'1', 'Actief'),
(5, 'Vintage Kicks', b'0', 'Sneakers', 'vintage_kicks.png', b'1', 'Bevestigd'),
(6, 'Barber & Cuts Lounge', b'0', 'Tattoo & Barbershop', 'barber_cuts.png', b'1', 'Actief'),
(7, 'Solebox Official', b'1', 'Sneakers', 'solebox.png', b'1', 'Actief'),
(8, 'Overkill Berlin', b'1', 'Sneakers & Apparel', 'overkill.png', b'1', 'Actief'),
(9, 'Patta Amsterdam', b'1', 'Streetwear', 'patta.png', b'1', 'Actief'),
(10, 'Woei Rotterdam', b'1', 'Sneakers', 'woei.png', b'1', 'Actief'),
(11, 'Sneakersnstuff', b'1', 'Sneakers', 'sns.png', b'1', 'Actief'),
(12, 'BSTN Store', b'1', 'Streetwear', 'bstn.png', b'1', 'Actief'),
(13, 'Outsole NL', b'0', 'Vintage Sneakers', NULL, b'1', 'Bevestigd'),
(14, 'Deadstock District', b'0', 'Sneakers', NULL, b'1', 'Actief'),
(15, 'Laces Out Supplies', b'0', 'Accessoires', NULL, b'1', 'Actief'),
(16, 'Crep Protect Hub', b'0', 'Sneaker Care', NULL, b'1', 'Actief'),
(17, 'Sneaker Cleaners RTM', b'0', 'Cleaning Service', NULL, b'1', 'Actief'),
(18, 'Heat on Feet', b'0', 'Sneakers', NULL, b'1', 'Bevestigd'),
(19, 'Grail Finder', b'0', 'Exclusive Kicks', NULL, b'1', 'In behandeling'),
(20, 'Urban Threads', b'0', 'Streetwear', NULL, b'1', 'Actief'),
(21, 'Daily Paper Archive', b'0', 'Streetwear', NULL, b'1', 'Actief'),
(22, 'Off the Hook', b'0', 'Apparel & Kicks', NULL, b'1', 'Bevestigd'),
(23, 'Prime Kicks Store', b'0', 'Sneakers', NULL, b'1', 'Actief'),
(24, 'Rotterdam Custom Lab', b'0', 'Customizer', NULL, b'1', 'Actief'),
(25, 'Sole Food BBQ', b'0', 'Eten en Drinken', NULL, b'1', 'Actief'),
(26, 'Sneaker Bites Coffee', b'0', 'Eten en Drinken', NULL, b'1', 'Actief'),
(27, 'The Good Will Out', b'0', 'Sneakers', NULL, b'1', 'Bevestigd'),
(28, 'Afew Store Pop-up', b'0', 'Sneakers & Streetwear', NULL, b'1', 'Actief'),
(29, 'Sneaker Freaker Mag', b'0', 'Media & Books', NULL, b'1', 'Actief'),
(30, 'Shoe Surgeon Academy', b'0', 'Workshop & Customizer', NULL, b'1', 'Actief'),
(31, 'Archive DNA', b'0', 'Rare Collectibles', NULL, b'1', 'In behandeling'),
(32, 'Dunk Master NL', b'0', 'Sneakers', NULL, b'1', 'Actief'),
(33, 'Air Max Heaven', b'0', 'Sneakers', NULL, b'1', 'Actief'),
(34, 'Yeezy Supply Club', b'0', 'Sneakers', NULL, b'1', 'Bevestigd'),
(35, 'Retro Jordan Vault', b'0', 'Sneakers', NULL, b'1', 'Actief'),
(36, 'Sneaker Sock Lab', b'0', 'Accessoires', NULL, b'1', 'Actief'),
(37, 'Pins & Patches Co.', b'0', 'Accessoires', NULL, b'1', 'Actief'),
(38, 'Ink & Lace Tattoo Studio', b'0', 'Tattoo & Barbershop', NULL, b'1', 'Actief'),
(39, 'Fade Masters RTM', b'0', 'Tattoo & Barbershop', NULL, b'1', 'Actief'),
(40, 'Kids Sneaker Corner', b'0', 'Kids Corner', NULL, b'1', 'Actief'),
(41, 'Mini Hypebeast NL', b'0', 'Kids Corner', NULL, b'1', 'Actief'),
(42, 'Street Food Burgers', b'0', 'Eten en Drinken', NULL, b'1', 'Actief'),
(43, 'Churros & Sweets Hub', b'0', 'Eten en Drinken', NULL, b'1', 'Actief'),
(44, 'Sneaker Display Cases', b'0', 'Accessoires', NULL, b'1', 'Bevestigd'),
(45, 'Rope Lace Supply', b'0', 'Accessoires', NULL, b'1', 'Actief'),
(46, 'Sole Revival Studio', b'0', 'Restoration', NULL, b'1', 'Actief'),
(47, 'Urban Graffiti Workshop', b'0', 'Art & Culture', NULL, b'1', 'Actief'),
(48, 'Sneakerness Rotterdam Merch', b'0', 'Merchandise', NULL, b'1', 'Actief');

-- ----------------------------------------------------------------------------
-- Contactpersonen
-- ----------------------------------------------------------------------------
INSERT INTO `Contactpersoon` (`Id`, `Naam`, `Telefoonnummer`, `Email`, `IsActief`, `Opmerking`) VALUES
(1, 'Mark de Vries', '06-12345678', 'mark@kicksrdam.nl', b'1', 'Eigenaar Kicks Rotterdam'),
(2, 'Sophie Jansen', '06-87654321', 'contact@solemate.nl', b'1', 'Manager SoleMate'),
(3, 'Bilal El Amrani', '06-22446688', 'info@streetwear-rtm.nl', b'1', 'Oprichter Streetwear RTM'),
(4, 'Emma Bakker', '06-99887766', 'art@sneakerstudio.nl', b'1', 'Hoofdartiest'),
(5, 'Daan van Leeuwen', '06-33557799', 'info@vintagekicks.nl', b'1', 'Vintage curator'),
(6, 'Jesse van Dijk', '06-11223344', 'jesse@barberandcuts.nl', b'1', 'Barber Lead'),
(7, 'Hikmet Sugoer', '06-11223301', 'contact@solebox.com', b'1', 'Solebox Partner Lead'),
(8, 'Marc Leuschner', '06-11223302', 'info@overkill.de', b'1', 'Overkill Lead'),
(9, 'Edson Sabajo', '06-11223303', 'edson@patta.nl', b'1', 'Patta Team'),
(10, 'Woei Tjin', '06-11223304', 'woei@woei.nl', b'1', 'Woei Team'),
(11, 'Peter Jansson', '06-11223305', 'peter@sns.se', b'1', 'SNS Lead'),
(12, 'Chris Bosz', '06-11223306', 'chris@bstn.com', b'1', 'BSTN Contact'),
(13, 'Sander Meijer', '06-44556677', 'sander@outsole.nl', b'1', 'Outsole Contact'),
(14, 'Lars de Jong', '06-55667788', 'lars@deadstock.nl', b'1', 'Deadstock Contact'),
(15, 'Robin Visser', '06-66778899', 'robin@lacesout.nl', b'1', 'Laces Out Contact'),
(16, 'Rizwan Ahmed', '06-77889900', 'riz@crepprotect.nl', b'1', 'Crep Protect Lead'),
(17, 'Kevin Smeets', '06-88990011', 'kevin@cleanersrtm.nl', b'1', 'Cleaners Lead'),
(18, 'Tim Brouwer', '06-99001122', 'tim@heatonfeet.nl', b'1', 'Heat on Feet Contact'),
(19, 'Sven Kuipers', '06-10203040', 'sven@grailfinder.nl', b'1', 'Grail Finder Contact'),
(20, 'Noah van Dijk', '06-20304050', 'noah@urbanthreads.nl', b'1', 'Urban Threads Contact'),
(21, 'Jefferson Osei', '06-30405060', 'jeff@dailypaper.nl', b'1', 'Daily Paper Lead'),
(22, 'David Cohen', '06-40506070', 'david@othook.nl', b'1', 'Off the Hook Contact'),
(23, 'Lucas Vos', '06-50607080', 'lucas@primekicks.nl', b'1', 'Prime Kicks Contact'),
(24, 'Milan de Groot', '06-60708090', 'milan@customlab.nl', b'1', 'Custom Lab Contact'),
(25, 'Marco Rossi', '06-70809001', 'marco@solefoodbbq.nl', b'1', 'Sole Food Lead'),
(26, 'Anouk Veenstra', '06-80900112', 'anouk@sneakerbites.nl', b'1', 'Bites Coffee Lead'),
(27, 'Alex Keller', '06-90011223', 'alex@tgwo.de', b'1', 'Good Will Out Contact'),
(28, 'Marco Biergen', '06-01122334', 'marco@afew.de', b'1', 'Afew Store Lead'),
(29, 'Woody Simon', '06-12233445', 'woody@sneakerfreaker.nl', b'1', 'Sneaker Freaker Contact'),
(30, 'Dominic Ciambrone', '06-23344556', 'dominic@shoesurgeon.nl', b'1', 'Academy Lead'),
(31, 'Ryan Chang', '06-34455667', 'ryan@archivedna.nl', b'1', 'Archive DNA Contact'),
(32, 'Stefan de Wit', '06-45566778', 'stefan@dunkmaster.nl', b'1', 'Dunk Master Contact'),
(33, 'Dennis Scholten', '06-56677889', 'dennis@airmaxheaven.nl', b'1', 'Air Max Heaven Contact'),
(34, 'Samir Bakkali', '06-67788990', 'samir@yeezysupply.nl', b'1', 'Yeezy Supply Contact'),
(35, 'Bram Hendriks', '06-78899001', 'bram@retrojordan.nl', b'1', 'Retro Jordan Contact'),
(36, 'Kelly van Loon', '06-89900112', 'kelly@socks.nl', b'1', 'Sock Lab Contact'),
(37, 'Iris Verschoor', '06-90011224', 'iris@pinsnl.nl', b'1', 'Pins & Patches Contact'),
(38, 'Boris van Dam', '06-01122336', 'boris@inklace.nl', b'1', 'Ink & Lace Lead'),
(39, 'Kareem Said', '06-12233448', 'kareem@fademasters.nl', b'1', 'Fade Masters Lead'),
(40, 'Laura Mulder', '06-23344560', 'laura@kidscorner.nl', b'1', 'Kids Corner Contact'),
(41, 'Bas van Beek', '06-34455672', 'bas@minihypebeast.nl', b'1', 'Mini Hypebeast Contact'),
(42, 'Giovanni Smit', '06-45566784', 'gio@streetfood.nl', b'1', 'Street Food Lead'),
(43, 'Maria Santos', '06-56677896', 'maria@churros.nl', b'1', 'Churros Lead'),
(44, 'Jeroen Koster', '06-67788008', 'jeroen@displaycases.nl', b'1', 'Cases Lead'),
(45, 'Daniël Post', '06-78899120', 'daniel@ropelaces.nl', b'1', 'Rope Lace Lead'),
(46, 'Floris Zeeman', '06-89900232', 'floris@solerevival.nl', b'1', 'Revival Lead'),
(47, 'Tyrell Jones', '06-90011344', 'tyrell@graffiti.nl', b'1', 'Graffiti Lead'),
(48, 'Organisatie Sneakerness', '06-01122456', 'merch@sneakerness.com', b'1', 'Official Merch Team');

-- ----------------------------------------------------------------------------
-- ContactPerVerkoper (koppeling 1-op-1 of veel-op-veel)
-- ----------------------------------------------------------------------------
INSERT INTO `ContactPerVerkoper` (`Id`, `VerkoperId`, `ContactpersoonId`, `IsActief`, `Opmerking`)
SELECT `Id`, `Id`, `Id`, b'1', 'Hoofdcontactpersoon' FROM `Verkoper`;

-- ----------------------------------------------------------------------------
-- Stands (18x AA+, 20x AA, 10x A = 48 bezet, 4 extra stands voor 52 bezet/55 totaal)
-- ----------------------------------------------------------------------------
-- Standtypes breakdown:
-- Verkoper 1, 4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22 -> 18x AA+
-- Verkoper 2, 5, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40 -> 20x AA
-- Verkoper 3, 6, 41, 42, 43, 44, 45, 46, 47, 48 -> 10x A

INSERT INTO `Stand` (`Id`, `VerkoperId`, `StandType`, `Prijs`, `AantalDagen`, `VerhuurdStatus`, `IsActief`, `Opmerking`) VALUES
-- 1 t/m 6 (exact uit het Figma design)
(1, 1, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-101 • Hal 1'),
(2, 2, 'AA',  600.00, 1, b'1', b'1', 'Stand #AA-104 • Hal 1'),
(3, 3, 'A',   450.00, 2, b'0', b'1', 'Stand #A-208 • Hal 2'),
(4, 4, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-112 • Hal 1'),
(5, 5, 'AA',  650.00, 2, b'1', b'1', 'Stand #A-201 • Hal 2'),
(6, 6, 'A',   400.00, 2, b'1', b'1', 'Stand #EX-05 • Hal 1 Lounge'),

-- Overige AA+ stands (totaal 18)
(7,  7,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-102 • Hal 1'),
(8,  8,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-103 • Hal 1'),
(9,  9,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-105 • Hal 1'),
(10, 10, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-106 • Hal 1'),
(11, 11, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-107 • Hal 1'),
(12, 12, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-108 • Hal 1'),
(13, 13, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-109 • Hal 1'),
(14, 14, 'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-110 • Hal 1'),
(15, 15, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-111 • Hal 1'),
(16, 16, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-113 • Hal 1'),
(17, 17, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-114 • Hal 1'),
(18, 18, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-115 • Hal 1'),
(19, 19, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-116 • Hal 1'),
(20, 20, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-117 • Hal 1'),
(21, 21, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-118 • Hal 1'),
(22, 22, 'AA+', 900.00, 2, b'1', b'1', 'Stand #AA-119 • Hal 1'),

-- Overige AA stands (totaal 20)
(23, 23, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-201 • Hal 2'),
(24, 24, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-202 • Hal 2'),
(25, 25, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-203 • Hal 2 Food Area'),
(26, 26, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-204 • Hal 2 Food Area'),
(27, 27, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-205 • Hal 2'),
(28, 28, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-206 • Hal 2'),
(29, 29, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-207 • Hal 2 Media'),
(30, 30, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-209 • Hal 2'),
(31, 31, 'AA', 600.00, 1, b'1', b'1', 'Stand #AA-210 • Hal 2'),
(32, 32, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-211 • Hal 2'),
(33, 33, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-212 • Hal 2'),
(34, 34, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-213 • Hal 2'),
(35, 35, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-214 • Hal 2'),
(36, 36, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-215 • Hal 2'),
(37, 37, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-216 • Hal 2'),
(38, 38, 'AA', 650.00, 2, b'1', b'1', 'Stand #AA-217 • Hal 2'),
(39, 39, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-218 • Hal 2'),
(40, 40, 'AA', 600.00, 2, b'1', b'1', 'Stand #AA-219 • Hal 2 Kids'),

-- Overige A stands (totaal 10)
(41, 41, 'A', 400.00, 2, b'1', b'1', 'Stand #A-202 • Hal 2 Kids'),
(42, 42, 'A', 450.00, 2, b'1', b'1', 'Stand #A-203 • Hal 2 Food'),
(43, 43, 'A', 400.00, 2, b'1', b'1', 'Stand #A-204 • Hal 2 Sweets'),
(44, 44, 'A', 400.00, 2, b'1', b'1', 'Stand #A-205 • Hal 2'),
(45, 45, 'A', 350.00, 1, b'1', b'1', 'Stand #A-206 • Hal 2'),
(46, 46, 'A', 400.00, 2, b'1', b'1', 'Stand #A-207 • Hal 2'),
(47, 47, 'A', 400.00, 2, b'1', b'1', 'Stand #A-209 • Hal 2 Art'),
(48, 48, 'A', 500.00, 2, b'1', b'1', 'Stand #A-01 • Hal 1 Main Entrance'),

-- Extra stands (totaal 55 stands in zaal, 52 bezet / 3 beschikbaar)
(49, 1,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-120 • Hal 1 Extra'),
(50, 4,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-121 • Hal 1 Extra'),
(51, 7,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-122 • Hal 1 Extra'),
(52, 9,  'AA+', 950.00, 2, b'1', b'1', 'Stand #AA-123 • Hal 1 Extra'),
(53, 1,  'A',   350.00, 1, b'0', b'1', 'Stand #A-210 • Hal 2 Beschikbaar'),
(54, 2,  'A',   350.00, 1, b'0', b'1', 'Stand #A-211 • Hal 2 Beschikbaar'),
(55, 3,  'A',   350.00, 1, b'0', b'1', 'Stand #A-212 • Hal 2 Beschikbaar');