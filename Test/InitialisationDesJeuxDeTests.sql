DROP DATABASE IF EXISTS check_your_mood_test ;
CREATE DATABASE IF NOT EXISTS `check_your_mood_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `check_your_mood_test`;

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



ALTER TABLE `humeur`
    ADD PRIMARY KEY (`codeHumeur`),
    ADD KEY `fk_Humeur_Libelle` (`libelle`),
    ADD KEY `fk_Humeur_Utilisateur` (`idUtil`);

--
-- Index pour la table `libelle`
--
ALTER TABLE `libelle`
    ADD PRIMARY KEY (`codeLibelle`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
    ADD PRIMARY KEY (`codeUtil`),
    ADD UNIQUE KEY `contrainte_identifiant` (`identifiant`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `humeur`
--
ALTER TABLE `humeur`
    MODIFY `codeHumeur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT pour la table `libelle`
--
ALTER TABLE `libelle`
    MODIFY `codeLibelle` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
    MODIFY `codeUtil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `humeur`
--
ALTER TABLE `humeur`
    ADD CONSTRAINT `fk_Humeur_Libelle` FOREIGN KEY (`libelle`) REFERENCES `libelle` (`codeLibelle`),
    ADD CONSTRAINT `fk_Humeur_Utilisateur` FOREIGN KEY (`idUtil`) REFERENCES `utilisateur` (`codeUtil`);

INSERT INTO utilisateur (prenom,nom,identifiant,mail,motDePasse) VALUES ('prenomTest1','nomTest1','idTest1','mail.test@test.test',MD5('TestMotDePasse'));
SET time_zone = "+01:00";

INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (1,'2021-01-01','00:00:00',1,'contexteTest1');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (1,'2021-01-01','00:40:00',1,'contexteTest2');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (1,'2021-01-01','01:00:00',1,'contexteTest3');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (2,'2021-01-01','01:40:00',1,'contexteTest4');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (2,'2021-01-01','02:00:00',1,'contexteTest5');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (7,'2021-02-01','02:40:00',1,'contexteTest6');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (13,'2022-01-01','03:00:00',1,'contexteTest7');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (22,'2023-03-01','03:40:00',1,'contexteTest8');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (18,'2022-01-01','04:00:00',1,'contexteTest9');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (11,'2021-01-25','04:40:00',1,'contexteTest10');
INSERT INTO humeur (libelle,dateHumeur,heure,idUtil,contexte) VALUES (19,'2021-01-02','05:00:00',1,'contexteTest11');