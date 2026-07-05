<?php
// ==========================
// API : ENVOYER UNE DEMANDE D'AMI
// ==========================

require_once "config.php";

$expediteurId = $_POST["expediteur_id"] ?? 0;
$destinataireId = $_POST["destinataire_id"] ?? 0;

if (empty($expediteurId) || empty($destinataireId)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

// Vérifie si une demande existe déjà
$verification = $pdo->prepare("
    SELECT id FROM demandes_amis
    WHERE (expediteur_id = ? AND destinataire_id = ?)
       OR (expediteur_id = ? AND destinataire_id = ?)
");
$verification->execute([$expediteurId, $destinataireId, $destinataireId, $expediteurId]);
$existant = $verification->fetch();

if ($existant) {
    echo json_encode(["statut" => 400, "message" => "Une demande existe déjà."]);
    exit;
}

// Crée la demande
$requete = $pdo->prepare("INSERT INTO demandes_amis (expediteur_id, destinataire_id) VALUES (?, ?)");
$requete->execute([$expediteurId, $destinataireId]);

echo json_encode(["statut" => 200, "message" => "Demande d'amitié envoyée."]);
?>
