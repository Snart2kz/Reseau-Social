<?php
// ==========================
// API : INSCRIPTION
// Crée un nouvel utilisateur dans la base de données
// ==========================

require_once "config.php";

// Récupération des données du formulaire
$nom = $_POST["nom"] ?? "";
$prenom = $_POST["prenom"] ?? "";
$email = $_POST["email"] ?? "";
$motDePasse = $_POST["mot_de_passe"] ?? "";

// Vérification que tous les champs sont remplis
if (empty($nom) || empty($prenom) || empty($email) || empty($motDePasse)) {
    echo json_encode(["statut" => 400, "message" => "Veuillez remplir tous les champs."]);
    exit;
}

// Vérification que l'email n'est pas déjà utilisé
$verification = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$verification->execute([$email]);

if ($verification->fetch()) {
    echo json_encode(["statut" => 400, "message" => "Cet email est déjà utilisé."]);
    exit;
}

// Hash du mot de passe pour la sécurité
$motDePasseHash = password_hash($motDePasse, PASSWORD_BCRYPT);

// Insertion dans la base de données
$requete = $pdo->prepare("
    INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe)
    VALUES (?, ?, ?, ?)
");
$requete->execute([$nom, $prenom, $email, $motDePasseHash]);

echo json_encode(["statut" => 200, "message" => "Inscription réussie ! Vous pouvez maintenant vous connecter."]);
?>
