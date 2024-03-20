CREATE TABLE `medecin` (
  `idMedecin` int(11) NOT NULL AUTO_INCREMENT,
  `Civilite` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Prenom` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Nom` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  PRIMARY KEY (`idMedecin`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = latin1 COLLATE = latin1_bin;

insert into
  `medecin` (`Civilite`, `Nom`, `Prenom`, `idMedecin`)
values
  ('1', 'dardet', 'lenny', 1);

insert into
  `medecin` (`Civilite`, `Nom`, `Prenom`, `idMedecin`)
values
  ('1', 'Goujon', 'Mael', 8);

CREATE TABLE `patient` (
  `idPatient` int(11) NOT NULL AUTO_INCREMENT,
  `Civilite` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Prenom` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Nom` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Adresse` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Code_postal` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Ville` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Date_de_naissance` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Lieu_de_naissance` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `Numero_Securite_Sociale` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `idMedecin` varchar(50) COLLATE latin1_bin NOT NULL,
  PRIMARY KEY (`idPatient`)
) ENGINE = InnoDB AUTO_INCREMENT = 17 DEFAULT CHARSET = latin1 COLLATE = latin1_bin;

insert into
  `patient` (
    `Adresse`,
    `Civilite`,
    `Code_postal`,
    `Date_de_naissance`,
    `Lieu_de_naissance`,
    `Nom`,
    `Numero_Securite_Sociale`,
    `Prenom`,
    `Ville`,
    `idMedecin`,
    `idPatient`
  )
values
  (
    '110 route de Narbonne',
    'Mme.',
    '31000',
    '14/05/1967',
    'Toulouse',
    'Dardet',
    '1231231231231',
    'Lenny',
    'Toulouse',
    '1',
    16
  );

insert into
  `patient` (
    `Adresse`,
    `Civilite`,
    `Code_postal`,
    `Date_de_naissance`,
    `Lieu_de_naissance`,
    `Nom`,
    `Numero_Securite_Sociale`,
    `Prenom`,
    `Ville`,
    `idMedecin`,
    `idPatient`
  )
values
  (
    '118 route de Narbonne',
    'Mr.',
    '31400',
    '14/08/1986',
    'Toulouse',
    'Goujon',
    '112238545566778',
    'Mael',
    'Toulouse',
    '1',
    2
  );

CREATE TABLE `consultation` (
  `idConsultation` int(11) NOT NULL AUTO_INCREMENT,
  `idPatient` int(11) DEFAULT NULL,
  `idMedecin` int(11) DEFAULT NULL,
  `DateConsultation` date DEFAULT NULL,
  `Heure` time DEFAULT NULL,
  `Duree` int(11) DEFAULT NULL,
  PRIMARY KEY (`idConsultation`),
  UNIQUE KEY `AK_consultation_idx2` (`idMedecin`, `DateConsultation`, `Heure`),
  UNIQUE KEY `AK_consultation_idx1` (`idPatient`, `DateConsultation`, `Heure`),
  KEY `idPatient` (`idPatient`),
  KEY `idMedecin` (`idMedecin`),
  CONSTRAINT `consultation_ibfk_1` FOREIGN KEY (`idPatient`) REFERENCES `patient` (`idPatient`),
  CONSTRAINT `consultation_ibfk_2` FOREIGN KEY (`idMedecin`) REFERENCES `medecin` (`idMedecin`)
) ENGINE = InnoDB AUTO_INCREMENT = 23 DEFAULT CHARSET = utf8mb4;

insert into
  `consultation` (
    `DateConsultation`,
    `Duree`,
    `Heure`,
    `idConsultation`,
    `idMedecin`,
    `idPatient`
  )
values
  ('2024-03-08', 60, '01:01:11', 1, 1, 16);

insert into
  `consultation` (
    `DateConsultation`,
    `Duree`,
    `Heure`,
    `idConsultation`,
    `idMedecin`,
    `idPatient`
  )
values
  ('2000-01-01', 15, '10:00:00', 22, 1, 16);

GRANT ALL PRIVILEGES ON PHP_Project.* TO 'etu' @'%' IDENTIFIED BY 'coucou';

FLUSH PRIVILEGES;