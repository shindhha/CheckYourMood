CREATE TABLE Tests (
    id INTEGER PRIMARY KEY,
    nomTest VARCHAR(255),
    dateTest DATE,
    description blob
);
INSERT INTO tests (id,nomTest) VALUES (1,'testDejaPresent');
CREATE TABLE `humeur` (
                          `codeHumeur` int(11) NOT NULL,
                          `libelle` int(2) NOT NULL,
                          `dateHumeur` date NOT NULL,
                          `heure` time NOT NULL,
                          `idUtil` int(11) NOT NULL,
                          `contexte` blob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `libelle` (
                           `codeLibelle` int(2) NOT NULL,
                           `libelleHumeur` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `emoji` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `libelle` (`codeLibelle`, `libelleHumeur`, `emoji`) VALUES
                                                                    (1, 'Admiration', '🤩'),
                                                                    (2, 'Adoration', '😍'),
                                                                    (3, 'Appréciation esthétique', '💖'),
                                                                    (4, 'Amusement', '😄'),
                                                                    (5, 'Colère', '😡'),
                                                                    (6, 'Anxiété', '😰'),
                                                                    (7, 'Émerveillement', '🥰'),
                                                                    (8, 'Malaise (embarrassement)', '😅'),
                                                                    (9, 'Ennui', '🥱'),
                                                                    (10, 'Calme (sérénité)', '😎'),
                                                                    (11, 'Confusion', '🤨'),
                                                                    (12, 'Envie (craving)', '🤤'),
                                                                    (13, 'Dégoût', '🤮'),
                                                                    (14, 'Douleur empathique', '💔'),
                                                                    (15, 'Intérêt étonné, intrigué', '🤔'),
                                                                    (16, 'Excitation (montée d’adrénaline)', '🤯'),
                                                                    (17, 'Peur', '😨'),
                                                                    (18, 'Horreur', '😱'),
                                                                    (19, 'Intérêt', '🧐'),
                                                                    (20, 'Joie', '😀'),
                                                                    (21, 'Nostalgie', '💭'),
                                                                    (22, 'Soulagement', '😌'),
                                                                    (23, 'Romance', '👩‍❤️‍💋‍👨'),
                                                                    (24, 'Tristesse', '🥺'),
                                                                    (25, 'Satisfaction', '😊'),
                                                                    (26, 'Désir sexuel', '😏'),
                                                                    (27, 'Surprise', '😮');

CREATE TABLE `utilisateur` (
                               `codeUtil` int(11) NOT NULL,
                               `prenom` varchar(30) NOT NULL,
                               `nom` varchar(30) NOT NULL,
                               `identifiant` varchar(30) NOT NULL,
                               `mail` varchar(30) NOT NULL,
                               `motDePasse` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO utilisateur (prenom,nom,pseudo,identifiant,mail,motDePasse) VALUES ('prenomTest1','nomTest1','idTest1','mail.test@test.test',MD5('TestMotDePasse'));
SET time_zone = "+01:00";