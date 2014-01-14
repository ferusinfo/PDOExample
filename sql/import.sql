CREATE TABLE `kat` (
  `id_kat` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(50) NOT NULL,
  `id_nadkat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_kat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `kat` (`id_kat`, `nazwa`, `id_nadkat`) VALUES
(1, 'Matematyka', NULL),
(2, 'Fizyka', NULL),
(3, 'Chemia', NULL),
(4, 'Algebra', 1),
(5, 'Dzialania na potegach', 4),
(6, 'Dzialania na pierwiastkach', 4),
(7, 'Pierwiastki drugiego stopnia', 6),
(8, 'Pierwiastki trzeciego stopnia', 6);

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(50) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id_user`, `login`, `haslo`) VALUES
(1, 'ania', 'ania123'),
(2, 'marek', 'marek123'),
(3, 'gacek', 'gacek123'),
(4, 'ewa', 'ewa123');

CREATE TABLE `zad` (
  `id_zad` int(11) NOT NULL AUTO_INCREMENT,
  `id_kat` int(11) NOT NULL,
  `nazwa` varchar(50) NOT NULL,
  `plik_pdf` varchar(50) NOT NULL,
  `trudnosc` int(1) NOT NULL,
  PRIMARY KEY (`id_zad`),
  KEY `id_kat` (`id_kat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `zad`
  ADD CONSTRAINT `zad_ibfk_1` FOREIGN KEY (`id_kat`) REFERENCES `kat` (`id_kat`) ON DELETE CASCADE ON UPDATE CASCADE;
