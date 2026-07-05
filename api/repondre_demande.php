<?php
// ==========================
// API : RÉPONDRE À UNE DEMANDE D'AMI
// Accepte ou refuse une demande
// ==========================

require_once "config.php";

$demandeId = $_POST["demande_id"] ?? 0;
$action = $_POST["action"] ?? ""; // "accepte" ou "refuse"

if (empty($demandeId) || empty($action)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

$requete = $pdo->prepare("UPDATE demandes_amis SET statut = ? WHERE id = ?");
$requete->execute([$action, $demandeId]);

echo json_encode(["statut" => 200, "message" => "Demande " . ($action === "accepte" ? "acceptée" : "refusée") . "."]);
?>
