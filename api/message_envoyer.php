<?php
// ==========================
// API : ENVOYER UN MESSAGE
// ==========================

require_once "config.php";

$expediteurId = $_POST["expediteur_id"] ?? 0;
$destinataireId = $_POST["destinataire_id"] ?? 0;
$contenu = $_POST["contenu"] ?? "";

if (empty($expediteurId) || empty($destinataireId) || empty($contenu)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

$requete = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, contenu) VALUES (?, ?, ?)");
$requete->execute([$expediteurId, $destinataireId, $contenu]);

echo json_encode(["statut" => 200, "message" => "Message envoyé."]);
?>
