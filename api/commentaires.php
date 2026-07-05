<?php
// ==========================
// API : LISTE DES COMMENTAIRES
// Retourne les commentaires d'un article
// ==========================

require_once "config.php";

$articleId = $_GET["article_id"] ?? 0;

$requete = $pdo->prepare("
    SELECT c.id, c.contenu, c.date_creation,
           u.id AS auteur_id, u.nom, u.prenom, u.photo AS photo_utilisateur
    FROM commentaires c
    JOIN utilisateurs u ON c.utilisateur_id = u.id
    WHERE c.article_id = ?
    ORDER BY c.date_creation ASC
");
$requete->execute([$articleId]);
$commentaires = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["statut" => 200, "commentaires" => $commentaires]);
?>
