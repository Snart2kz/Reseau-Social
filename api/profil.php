<?php
// ==========================
// API : PROFIL
// Retourne les informations d'un utilisateur
// ==========================

require_once "config.php";

$id = $_GET["id"] ?? 0;

$requete = $pdo->prepare("SELECT id, nom, prenom, email, photo, role, date_inscription FROM utilisateurs WHERE id = ?");
$requete->execute([$id]);
$profil = $requete->fetch(PDO::FETCH_ASSOC);

if ($profil) {
    echo json_encode(["statut" => 200, "profil" => $profil]);
} else {
    echo json_encode(["statut" => 404, "message" => "Utilisateur introuvable."]);
}
?>
