<?php
// ==========================
// API : SUPPRESSION ADMIN
// Supprime un utilisateur ou un article
// ==========================

require_once "config.php";

$type = $_POST["type"] ?? "";
$id = $_POST["id"] ?? 0;

if (empty($type) || empty($id)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

if ($type === "utilisateur") {
    $requete = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $requete->execute([$id]);
} elseif ($type === "article") {
    $requete = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $requete->execute([$id]);
} else {
    echo json_encode(["statut" => 400, "message" => "Type invalide."]);
    exit;
}

echo json_encode(["statut" => 200, "message" => "Supprimé avec succès."]);
?>
