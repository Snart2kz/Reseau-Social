<?php
// ==========================
// API : CHANGER LE MOT DE PASSE
// Vérifie l'ancien mot de passe et le remplace par le nouveau
// ==========================

require_once "config.php";

$id = $_POST["id"] ?? 0;
$ancienMotDePasse = $_POST["ancien_mot_de_passe"] ?? "";
$nouveauMotDePasse = $_POST["nouveau_mot_de_passe"] ?? "";

if (empty($ancienMotDePasse) || empty($nouveauMotDePasse)) {
    echo json_encode(["statut" => 400, "message" => "Veuillez remplir tous les champs."]);
    exit;
}

// Récupère l'ancien mot de passe hashé
$requete = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
$requete->execute([$id]);
$utilisateur = $requete->fetch();

// Vérifie l'ancien mot de passe
if (!password_verify($ancienMotDePasse, $utilisateur["mot_de_passe"])) {
    echo json_encode(["statut" => 400, "message" => "L'ancien mot de passe est incorrect."]);
    exit;
}

// Hash et enregistre le nouveau mot de passe
$nouveauHash = password_hash($nouveauMotDePasse, PASSWORD_BCRYPT);
$miseAJour = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
$miseAJour->execute([$nouveauHash, $id]);

echo json_encode(["statut" => 200, "message" => "Mot de passe changé avec succès."]);
?>
