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

-- Création de la table `exam_access`
CREATE TABLE exam_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    question_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Création de la table `options`
CREATE TABLE options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    option_letter CHAR(1) NOT NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Création de la table `questions`
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    exam_id INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Création de la table `retake_requests`
CREATE TABLE retake_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    approved TINYINT(1) DEFAULT 0,
    request_time DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- Création de la table `student_scores`
CREATE TABLE student_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    score INT NOT NULL,
    attempt_time DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(2, 'student1', '57d38b4f66fc269161a950a389ec2978', 'student', 'DSE');

-- Configuration de l'auto-incrémentation pour correspondre aux données existantes
ALTER TABLE users AUTO_INCREMENT = 43;
ALTER TABLE exams AUTO_INCREMENT = 4;
ALTER TABLE exam_access AUTO_INCREMENT = 125;
ALTER TABLE options AUTO_INCREMENT = 133;
ALTER TABLE questions AUTO_INCREMENT = 36;
ALTER TABLE student_answers AUTO_INCREMENT = 27;
ALTER TABLE student_scores AUTO_INCREMENT = 5;
```
