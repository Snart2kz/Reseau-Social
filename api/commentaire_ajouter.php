<?php
// ==========================
// API : AJOUTER UN COMMENTAIRE
// ==========================

require_once "config.php";

$articleId = $_POST["article_id"] ?? 0;
$utilisateurId = $_POST["utilisateur_id"] ?? 0;
$contenu = $_POST["contenu"] ?? "";

if (empty($articleId) || empty($utilisateurId) || empty($contenu)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

$requete = $pdo->prepare("INSERT INTO commentaires (utilisateur_id, article_id, contenu) VALUES (?, ?, ?)");
$requete->execute([$utilisateurId, $articleId, $contenu]);

echo json_encode(["statut" => 200, "message" => "Commentaire ajouté."]);
?>
