-- SocialNet ESGIS - Script de création de la base de données (v2)

CREATE DATABASE IF NOT EXISTS socialnet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE socialnet;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    role ENUM('user', 'moderateur', 'admin') NOT NULL DEFAULT 'user',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des publications
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contenu TEXT,
    image VARCHAR(255) DEFAULT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des commentaires
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des likes / dislikes
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    type ENUM('like', 'dislike') NOT NULL,
    UNIQUE KEY unique_like_user_post (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Table des relations d'amitié
CREATE TABLE friends (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'refused') NOT NULL DEFAULT 'pending',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des messages privés
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT,
    image VARCHAR(255) DEFAULT NULL,
    date_message DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des tokens (réinitialisation mot de passe)
CREATE TABLE tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiration DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des tokens d'authentification (équivalent "session" stockée côté serveur,
-- le client garde le token dans sessionStorage et le renvoie via le header Authorization)
CREATE TABLE auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================================
-- Comptes de test (mot de passe pour tous : "Test1234")
-- Hash généré avec password_hash('Test1234', PASSWORD_DEFAULT)
-- =========================================
INSERT INTO users (nom, prenom, email, mot_de_passe, role, bio) VALUES
('Diallo', 'Hawiz', 'admin@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', 'admin', 'Administrateur de la plateforme'),
('Diakité', 'Moussa', 'modo@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', 'moderateur', 'Modérateur'),
('Kourouma', 'Aïcha', 'user@socialnet.test', '$2b$10$zRzpXAG7EQofI4bcFTDeWOOfBlgJ0yknIXUGx7I4wQFwwWdxHp5vC', 'user', 'Étudiante L2 IRT');
