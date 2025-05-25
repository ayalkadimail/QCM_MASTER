```sql
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS qcm;
USE qcm;

-- Création de la table `exams`
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filiere VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `exams`
INSERT INTO exams (id, filiere, name) VALUES
(1, 'DSE', 'Examen DSE'),
(2, 'Master', 'Examen Master'),
(3, 'DS', 'Examen DS');

-- Création de la table `exam_access`
CREATE TABLE exam_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    question_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `exam_access`
INSERT INTO exam_access (id, student_id, question_id) VALUES
(96, 2, 1),
(97, 2, 2),
(98, 2, 3),
(99, 2, 5),
(100, 2, 6),
(101, 2, 7),
(102, 2, 8),
(103, 2, 9),
(104, 2, 10),
(105, 2, 11),
(116, 21, 25),
(117, 21, 26),
(118, 21, 27),
(119, 21, 28),
(120, 21, 29),
(121, 21, 30),
(122, 21, 31),
(123, 21, 32),
(124, 21, 33);

-- Création de la table `options`
CREATE TABLE options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    option_letter CHAR(1) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `options`
INSERT INTO options (id, question_id, option_text, is_correct, option_letter) VALUES
(1, 1, 'Un langage côté serveur', 1, 'A'),
(2, 1, 'Une base de données', 0, 'B'),
(3, 1, 'Un framework frontend', 0, 'C'),
(4, 1, 'Un protocole', 0, 'D'),
(5, 2, 'Un langage de programmation', 0, 'A'),
(6, 2, 'Une base de données relationnelle', 1, 'B'),
(7, 2, 'Un serveur web', 0, 'C'),
(8, 2, 'Un navigateur', 0, 'D'),
(9, 3, 'Unité Centrale de Traitement', 0, 'A'),
(10, 3, 'Unité Personnelle d\'Ordinateur', 0, 'B'),
(11, 3, 'Unité Centrale de Traitement', 1, 'C'),
(12, 3, 'Unité de Panneau de Contrôle', 0, 'D'),
(21, 5, 'Structurer le contenu sur le web', 1, 'A'),
(22, 5, 'Styliser les pages web', 0, 'B'),
(23, 5, 'Script côté serveur', 0, 'C'),
(24, 5, 'Gestion de base de données', 0, 'D'),
(25, 6, 'MySQL', 0, 'A'),
(26, 6, 'PostgreSQL', 0, 'B'),
(27, 6, 'MongoDB', 1, 'C'),
(28, 6, 'SQLite', 0, 'D'),
(29, 7, 'Interface de Programmation d\'Application', 1, 'A'),
(30, 7, 'Intégration Automatisée de Processus', 0, 'B'),
(31, 7, 'Interface de Protocole Avancé', 0, 'C'),
(32, 7, 'Intégration de Processus d\'Application', 0, 'D'),
(33, 8, 'Styliser les pages web', 1, 'A'),
(34, 8, 'Structurer le contenu web', 0, 'B'),
(35, 8, 'Script côté serveur', 0, 'C'),
(36, 8, 'Requêtes de base de données', 0, 'D'),
(37, 9, 'Python', 0, 'A'),
(38, 9, 'Java', 1, 'B'),
(39, 9, 'C++', 0, 'C'),
(40, 9, 'PHP', 0, 'D'),
(41, 10, 'Gérer les bases de données', 0, 'A'),
(42, 10, 'Suivre les changements de code', 1, 'B'),
(43, 10, 'Compiler le code', 0, 'C'),
(44, 10, 'Exécuter des serveurs web', 0, 'D'),
(45, 11, 'Langage de Requête Structuré', 1, 'A'),
(46, 11, 'Logique de Requête Simple', 0, 'B'),
(47, 11, 'Langage de Requête Système', 0, 'C'),
(48, 11, 'Logique de Requête Séquencée', 0, 'D'),
(49, 15, 'O(log n)', 1, 'A'),
(50, 15, 'O(n)', 0, 'B'),
(51, 15, 'O(n^2)', 0, 'C'),
(52, 15, 'O(1)', 0, 'D'),
(53, 16, 'Pile', 0, 'A'),
(54, 16, 'File', 1, 'B'),
(55, 16, 'Tas', 0, 'C'),
(56, 16, 'Arbre Binaire', 0, 'D'),
(57, 17, 'Réduire la redondance des données', 1, 'A'),
(58, 17, 'Augmenter la duplication des données', 0, 'B'),
(59, 17, 'Chiffrer les données', 0, 'C'),
(60, 17, 'Compresser les données', 0, 'D'),
(61, 18, 'Fournir une base pour les sous-classes', 1, 'A'),
(62, 18, 'Créer des objets autonomes', 0, 'B'),
(63, 18, 'Stocker uniquement des données', 0, 'C'),
(64, 18, 'Exécuter des méthodes statiques', 0, 'D'),
(65, 19, 'Tri à Bulles', 0, 'A'),
(66, 19, 'Tri Rapide', 1, 'B'),
(67, 19, 'Tri par Sélection', 0, 'C'),
(68, 19, 'Tri par Insertion', 0, 'D'),
(69, 20, 'Processus en attente indéfinie', 1, 'A'),
(70, 20, 'Un crash système', 0, 'B'),
(71, 20, 'Une fuite de mémoire', 0, 'C'),
(72, 20, 'Une panne réseau', 0, 'D'),
(73, 21, 'Atomicité, Cohérence, Isolation, Durabilité', 1, 'A'),
(74, 21, 'Accès, Contrôle, Intégration, Données', 0, 'B'),
(75, 21, 'Disponibilité, Cohérence, Isolation, Fiabilité', 0, 'C'),
(76, 21, 'Atomicité, Contrôle, Intégration, Durabilité', 0, 'D'),
(77, 22, 'Permet le polymorphisme', 1, 'A'),
(78, 22, 'Augmente la vitesse de compilation', 0, 'B'),
(79, 22, 'Réduit l\'utilisation de la mémoire', 0, 'C'),
(80, 22, 'Simplifie la syntaxe', 0, 'D'),
(81, 23, 'HTTP', 0, 'A'),
(82, 23, 'HTTPS', 1, 'B'),
(83, 23, 'FTP', 0, 'C'),
(84, 23, 'SMTP', 0, 'D'),
(85, 24, 'Décrit l\'efficacité de l\'algorithme', 1, 'A'),
(86, 24, 'Mesure la taille de la mémoire', 0, 'B'),
(87, 24, 'Compte les lignes de code', 0, 'C'),
(88, 24, 'Suit le temps d\'exécution', 0, 'D'),
(89, 25, 'Modèle trop complexe pour les données', 1, 'A'),
(90, 25, 'Modèle trop simple pour les données', 0, 'B'),
(91, 25, 'Modèle sans erreurs', 0, 'C'),
(92, 25, 'Modèle avec un biais élevé', 0, 'D'),
(93, 26, 'K-Means', 0, 'A'),
(94, 26, 'Régression Logistique', 1, 'B'),
(95, 26, 'Régression Linéaire', 0, 'C'),
(96, 26, 'DBSCAN', 0, 'D'),
(97, 27, 'Évaluer les performances de classification', 1, 'A'),
(98, 27, 'Visualiser la distribution des données', 0, 'B'),
(99, 27, 'Réduire la dimensionnalité', 0, 'C'),
(100, 27, 'Calculer les corrélations', 0, 'D'),
(101, 28, 'Analyse en Composantes Principales', 1, 'A'),
(102, 28, 'Analyse de Corrélation Partielle', 0, 'B'),
(103, 28, 'Algorithme de Clustering Prédictif', 0, 'C'),
(104, 28, 'Analyse de Composantes Proportionnelles', 0, 'D'),
(105, 29, 'NumPy', 0, 'A'),
(106, 29, 'Matplotlib', 1, 'B'),
(107, 29, 'Pandas', 0, 'C'),
(108, 29, 'SciPy', 0, 'D'),
(109, 30, 'Probabilité de l\'hypothèse nulle', 1, 'A'),
(110, 30, 'Pourcentage de variance', 0, 'B'),
(111, 30, 'Mesure de la taille de l\'effet', 0, 'C'),
(112, 30, 'Coefficient de corrélation', 0, 'D'),
(113, 31, 'Mesure l\'erreur de prédiction', 1, 'A'),
(114, 31, 'Sélectionne les caractéristiques', 0, 'B'),
(115, 31, 'Regroupe les données', 0, 'C'),
(116, 31, 'Normalise les données', 0, 'D'),
(117, 32, 'Imputation', 1, 'A'),
(118, 32, 'Normalisation', 0, 'B'),
(119, 32, 'Encodage', 0, 'C'),
(120, 32, 'Mise à l\'échelle', 0, 'D'),
(121, 33, 'Modèle inspiré des neurones du cerveau', 1, 'A'),
(122, 33, 'Système de gestion de base de données', 0, 'B'),
(123, 33, 'Algorithme de tri', 0, 'C'),
(124, 33, 'Système de contrôle de version', 0, 'D'),
(129, 35, 'Évaluer les performances du modèle', 1, 'A'),
(130, 35, 'Réduire la taille du jeu de données', 0, 'B'),
(131, 35, 'Augmenter la vitesse d\'entraînement', 0, 'C'),
(132, 35, 'Nettoyer les données', 0, 'D');

-- Création de la table `questions`
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    exam_id INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `questions`
INSERT INTO questions (id, question_text, exam_id) VALUES
(1, 'Qu\'est-ce que PHP ?', 1),
(2, 'Qu\'est-ce que MySQL ?', 1),
(3, 'Que signifie CPU ?', 1),
(5, 'À quoi sert HTML ?', 1),
(6, 'Laquelle des suivantes est une base de données NoSQL ?', 1),
(7, 'Que signifie API ?', 1),
(8, 'À quoi sert CSS ?', 1),
(9, 'Quel langage est principalement utilisé pour le développement d\'applications Android ?', 1),
(10, 'Quel est le but d\'un système de contrôle de version ?', 1),
(11, 'Que signifie SQL ?', 1),
(15, 'Quelle est la complexité temporelle d\'une recherche binaire ?', 2),
(16, 'Quelle structure de données est utilisée dans une recherche en largeur ?', 2),
(17, 'Qu\'est-ce que la normalisation dans la conception de bases de données ?', 2),
(18, 'Quel est le but d\'une classe abstraite ?', 2),
(19, 'Quel algorithme de tri a les meilleures performances en moyenne ?', 2),
(20, 'Qu\'est-ce qu\'un deadlock dans les systèmes d\'exploitation ?', 2),
(21, 'Que signifie ACID dans les transactions de base de données ?', 2),
(22, 'Quel est le rôle d\'une fonction virtuelle en C++ ?', 2),
(23, 'Quel protocole est utilisé pour une communication web sécurisée ?', 2),
(24, 'Quelle est la signification de la notation Big-O ?', 2),
(25, 'Qu\'est-ce que le surajustement (overfitting) en apprentissage automatique ?', 3),
(26, 'Quel algorithme est utilisé pour les tâches de classification ?', 3),
(27, 'Quel est le but d\'une matrice de confusion ?', 3),
(28, 'Que signifie PCA dans l\'analyse de données ?', 3),
(29, 'Quelle bibliothèque Python est utilisée pour la visualisation de données ?', 3),
(30, 'Qu\'est-ce qu\'une valeur p en statistiques ?', 3),
(31, 'Quel est le rôle d\'une fonction de perte en apprentissage automatique ?', 3),
(32, 'Quelle technique est utilisée pour gérer les données manquantes ?', 3),
(33, 'Qu\'est-ce qu\'un réseau de neurones ?', 3),
(35, 'Quel est le but de la validation croisée ?', 3);

-- Création de la table `retake_requests`
CREATE TABLE retake_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    approved TINYINT(1) DEFAULT 0,
    request_time DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pas de données dans `retake_requests` selon les informations fournies

-- Création de la table `student_answers`
CREATE TABLE student_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    submitted_at DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES options(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `student_answers`
INSERT INTO student_answers (id, student_id, question_id, selected_option_id, is_correct, submitted_at) VALUES
(1, 39, 1, 1, 1, '2025-05-19 22:43:41'),
(2, 39, 2, 6, 1, '2025-05-19 22:43:41'),
(3, 39, 3, 11, 1, '2025-05-19 22:43:41'),
(4, 40, 1, 1, 1, '2025-05-19 23:00:25'),
(5, 40, 2, 6, 1, '2025-05-19 23:00:25'),
(6, 40, 3, 11, 1, '2025-05-19 23:00:25'),
(7, 2, 1, 1, 1, '2025-05-20 00:30:23'),
(8, 2, 2, 5, 0, '2025-05-20 00:30:24'),
(9, 2, 3, 9, 0, '2025-05-20 00:30:24'),
(10, 2, 5, 21, 1, '2025-05-20 00:30:24'),
(11, 2, 6, 25, 0, '2025-05-20 00:30:24'),
(12, 2, 7, 29, 1, '2025-05-20 00:30:24'),
(13, 2, 8, 33, 1, '2025-05-20 00:30:24'),
(14, 2, 9, 38, 1, '2025-05-20 00:30:24'),
(15, 2, 10, 43, 0, '2025-05-20 00:30:24'),
(16, 2, 11, 45, 1, '2025-05-20 00:30:24'),
(17, 11, 15, 49, 1, '2025-05-20 00:31:28'),
(18, 11, 16, 53, 0, '2025-05-20 00:31:28'),
(19, 11, 17, 57, 1, '2025-05-20 00:31:28'),
(20, 11, 18, 61, 1, '2025-05-20 00:31:28'),
(21, 11, 19, 68, 0, '2025-05-20 00:31:28'),
(22, 11, 20, 70, 0, '2025-05-20 00:31:28'),
(23, 11, 21, 73, 1, '2025-05-20 00:31:28'),
(24, 11, 22, 78, 0, '2025-05-20 00:31:28'),
(25, 11, 23, 81, 0, '2025-05-20 00:31:28'),
(26, 11, 24, 85, 1, '2025-05-20 00:31:28');

-- Création de la table `student_scores`
CREATE TABLE student_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    score INT NOT NULL,
    attempt_time DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `student_scores`
INSERT INTO student_scores (id, student_id, score, attempt_time) VALUES
(1, 39, 3, '2025-05-19 22:43:41'),
(2, 40, 3, '2025-05-19 23:00:25'),
(3, 2, 2, '2025-05-20 00:30:24'),
(4, 11, 0, '2025-05-20 00:31:28');

-- Création de la table `users`
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('teacher', 'student') NOT NULL,
    filiere VARCHAR(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des données dans `users`
INSERT INTO users (id, username, password, role, filiere) VALUES
(1, 'teacher1', '11eb408e6d06cc41e166b2acc58f2cfb', 'teacher', NULL),
(2, 'student1', '57d38b4f66fc269161a950a389ec2978', 'student', 'DSE'),
(3, 'student2', 'cdba5dd9ee04cd4433bd2786277ba539', 'student', 'DSE'),
(4, 'student4', '32211ba97b59a1547e813e57955ef055', 'student', 'DSE'),
(5, 'student5', '1a3018ad4798921edbf603dcb5ad70e6', 'student', 'DSE'),
(6, 'student6', '282a3928f051c4feeca5964bbc5f6ba6', 'student', 'DSE'),
(7, 'student7', '4e703d599d3f9993e80c4eba6b9ef26c', 'student', 'DSE'),
(8, 'student8', 'b33e6a7de0f6f75794e074a36666f00e', 'student', 'DSE'),
(9, 'student9', '1794fb83cb525ddd8ff509f7ac395d3d', 'student', 'DSE'),
(10, 'student10', '916d40c1924773dad44b03cd9e079b79', 'student', 'DSE'),
(11, 'student11', '17b316f65e6ca899fb6db3e04e221bf3', 'student', 'Master'),
(12, 'student12', '1b47ad62dee4c078f9b4543fad9164ac', 'student', 'Master'),
(13, 'student13', 'caa39d96ac000ffd30b3d648c5181f90', 'student', 'Master'),
(14, 'student14', '21dd98bd1a8307193525832e4f99052e', 'student', 'Master'),
(15, 'student15', '315c4de68c2006d41f609ca730d9d31e', 'student', 'Master'),
(16, 'student16', 'ebc059de293e166dc2a73dc51949e741', 'student', 'Master'),
(17, 'student17', '531544a1fd786059285e2ec33be0f43a', 'student', 'Master'),
(18, 'student18', 'd50c41d8022417474a0d854d086c4d80', 'student', 'Master'),
(19, 'student19', 'c513dd4fffb55797f77b093b15a58c92', 'student', 'Master'),
(20, 'student20', '71b9ac83f9369694bdecf5813306d025', 'student', 'Master'),
(21, 'student21', '2c78268748a7f762607624330b310d73', 'student', 'DS'),
(22, 'student22', 'd4f0d46fb64e006dfcfbe48b75ab233b', 'student', 'DS'),
(23, 'student23', '2eb6b4449d2c69800c651ae840cf5a2c', 'student', 'DS'),
(24, 'student24', '2d5c98658c19168e9701d6dbbded4134', 'student', 'DS'),
(25, 'student25', '8475c3396ac0717366de024af704a90b', 'student', 'DS'),
(26, 'student26', 'b2044c1662bc3bb32d865b777f8897ee', 'student', 'DS'),
(27, 'student27', '47755beb04d6f53856cbd7d8b4dc0767', 'student', 'DS'),
(39, 'student31', '979bb90a7d0e7ac0f6885f336daae1f6', 'student', 'DSE'),
(40, 'student32', '92646a90ec6319d000815eaa0d29f43a', 'student', 'DSE'),
(42, 'student28', '065b5a5fb2acd0f5c980fbac8a382224', 'student', 'DS');

-- Configuration de l'auto-incrémentation pour correspondre aux données existantes
ALTER TABLE users AUTO_INCREMENT = 43;
ALTER TABLE exams AUTO_INCREMENT = 4;
ALTER TABLE exam_access AUTO_INCREMENT = 125;
ALTER TABLE options AUTO_INCREMENT = 133;
ALTER TABLE questions AUTO_INCREMENT = 36;
ALTER TABLE student_answers AUTO_INCREMENT = 27;
ALTER TABLE student_scores AUTO_INCREMENT = 5;
```
