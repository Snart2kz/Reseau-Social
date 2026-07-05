-- ==========================
-- SCRIPT DE CRÉATION DE LA BASE DE DONNÉES
-- social_network
-- ==========================

CREATE DATABASE IF NOT EXISTS social_network;
USE social_network;

-- ==========================
-- TABLE : utilisateurs
-- Stocke les informations de tous les utilisateurs
-- ==========================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT 'default.png',
    role ENUM('utilisateur', 'moderateur', 'administrateur') DEFAULT 'utilisateur',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- TABLE : articles
-- Stocke les publications des utilisateurs
-- ==========================
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- ==========================
-- TABLE : likes
-- Stocke les likes et dislikes sur les articles
-- ==========================
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    article_id INT NOT NULL,
    type ENUM('like', 'dislike') NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (utilisateur_id, article_id)
);

-- ==========================
-- TABLE : commentaires
-- Stocke les commentaires sur les articles
-- ==========================
CREATE TABLE IF NOT EXISTS commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    article_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- ==========================
-- TABLE : demandes_amis
-- Stocke les demandes d'amitié entre utilisateurs
-- ==========================
CREATE TABLE IF NOT EXISTS demandes_amis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_demande (expediteur_id, destinataire_id)
);

-- ==========================
-- TABLE : messages
-- Stocke les messages du chat
-- ==========================
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    contenu TEXT,
    image VARCHAR(255) DEFAULT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- ==========================
-- TABLE : tokens_reset
-- Stocke les tokens pour la réinitialisation du mot de passe
-- ==========================
CREATE TABLE IF NOT EXISTS tokens_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    date_expiration DATETIME NOT NULL,
    FOREIGN KEY (email) REFERENCES utilisateurs(email) ON DELETE CASCADE
);

-- ==========================
-- ADMINISTRATEUR PAR DÉFAUT
-- Email : admin@social.com
-- Mot de passe : admin123
-- ==========================
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Super', 'admin@social.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur');
