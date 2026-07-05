<?php
// ==========================
// API : LIKES / DISLIKES
// Ajoute ou supprime un like/dislike sur un article
// ==========================

require_once "config.php";

$articleId = $_POST["article_id"] ?? 0;
$type = $_POST["type"] ?? "";
$utilisateurId = $_POST["utilisateur_id"] ?? 0;

if (empty($articleId) || empty($type) || empty($utilisateurId)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

// Vérifie si l'utilisateur a déjà réagi à cet article
$verification = $pdo->prepare("SELECT id, type FROM likes WHERE utilisateur_id = ? AND article_id = ?");
$verification->execute([$utilisateurId, $articleId]);
$existant = $verification->fetch();

if ($existant) {
    if ($existant["type"] === $type) {
        // Si le même type, on supprime (toggle)
        $suppression = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $suppression->execute([$existant["id"]]);
    } else {
        // Si le type est différent, on met à jour
        $miseAJour = $pdo->prepare("UPDATE likes SET type = ? WHERE id = ?");
        $miseAJour->execute([$type, $existant["id"]]);
    }
} else {
    // Nouvelle réaction
    $ajout = $pdo->prepare("INSERT INTO likes (utilisateur_id, article_id, type) VALUES (?, ?, ?)");
    $ajout->execute([$utilisateurId, $articleId, $type]);
}

echo json_encode(["statut" => 200, "message" => "Réaction mise à jour."]);
?>
