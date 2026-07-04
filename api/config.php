<?php
// ==========================
// CONFIGURATION DE LA BASE DE DONNÉES
// Utilise SQLite pour la démonstration (compatible MySQL)
// Pour MySQL, décommentez la section MySQL et commentez SQLite
// ==========================

// ==========================
// MODE SQLITE (pour démonstration)
// ==========================
$dbPath = __DIR__ . "/../database.sqlite";
$estSqlite = true;

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON");

    // Crée les tables automatiquement si elles n'existent pas
    initialiserTables();
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// ==========================
// MODE MYSQL (pour production)
// Décommentez ce bloc et commentez le bloc SQLite ci-dessus
// ==========================
/*
$estSqlite = false;
$host = "localhost";
$dbname = "social_network";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
*/

// ==========================
// INITIALISATION DES TABLES
// ==========================
function initialiserTables() {
    global $pdo, $estSqlite;

    // Vérifie si la table utilisateurs existe déjà
    $resultat = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='utilisateurs'");
    if ($resultat->fetch()) {
        return; // Les tables existent déjà
    }

    // Crée toutes les tables
    $pdo->exec("
        CREATE TABLE utilisateurs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT NOT NULL,
            prenom TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            mot_de_passe TEXT NOT NULL,
            photo TEXT DEFAULT 'default.png',
            role TEXT DEFAULT 'utilisateur',
            date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            description TEXT NOT NULL,
            image TEXT DEFAULT NULL,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE likes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            article_id INTEGER NOT NULL,
            type TEXT NOT NULL,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
            UNIQUE(utilisateur_id, article_id)
        )
    ");

    $pdo->exec("
        CREATE TABLE commentaires (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            utilisateur_id INTEGER NOT NULL,
            article_id INTEGER NOT NULL,
            contenu TEXT NOT NULL,
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE demandes_amis (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            expediteur_id INTEGER NOT NULL,
            destinataire_id INTEGER NOT NULL,
            statut TEXT DEFAULT 'en_attente',
            date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            UNIQUE(expediteur_id, destinataire_id)
        )
    ");

    $pdo->exec("
        CREATE TABLE messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            expediteur_id INTEGER NOT NULL,
            destinataire_id INTEGER NOT NULL,
            contenu TEXT,
            image TEXT DEFAULT NULL,
            date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE tokens_reset (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token TEXT NOT NULL,
            date_expiration DATETIME NOT NULL,
            FOREIGN KEY (email) REFERENCES utilisateurs(email) ON DELETE CASCADE
        )
    ");

    // Crée un administrateur par défaut
    $motDePasseHash = password_hash("admin123", PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)")
        ->execute(["Admin", "Super", "admin@social.com", $motDePasseHash, "administrateur"]);
}
?>
