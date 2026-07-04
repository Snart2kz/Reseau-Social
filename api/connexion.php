<?php
// ==========================
// API : CONNEXION
// Vérifie l'email et le mot de passe
// Retourne les données de l'utilisateur si succès
// ==========================

require_once "config.php";

// On récupère les données du formulaire
$email = $_POST["email"] ?? "";
$motDePasse = $_POST["mot_de_passe"] ?? "";

// Vérification que les champs ne sont pas vides
if (empty($email) || empty($motDePasse)) {
    echo json_encode(["statut" => 400, "message" => "Veuillez remplir tous les champs."]);
    exit;
}

// On cherche l'utilisateur dans la base de données
$requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$requete->execute([$email]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

// Vérification du mot de passe
if ($utilisateur && password_verify($motDePasse, $utilisateur["mot_de_passe"])) {
    // Succès : on retourne les données de l'utilisateur
    echo json_encode([
        "statut" => 200,
        "message" => "Connexion réussie.",
        "utilisateur" => [
            "id" => $utilisateur["id"],
            "nom" => $utilisateur["nom"],
            "prenom" => $utilisateur["prenom"],
            "email" => $utilisateur["email"],
            "photo" => $utilisateur["photo"],
            "role" => $utilisateur["role"]
        ]
    ]);
} else {
    // Erreur : email ou mot de passe incorrect
    echo json_encode(["statut" => 401, "message" => "Email ou mot de passe incorrect."]);
}
?>
