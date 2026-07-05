<?php
// ==========================
// API : LISTE DES ARTICLES
// Retourne tous les articles avec les infos utilisateur et les likes
// ==========================

require_once "config.php";

$utilisateurId = $_GET["utilisateur_id"] ?? 0;

// Récupère tous les articles avec les informations de l'auteur
$requete = $pdo->query("
    SELECT
        a.id, a.description, a.image, a.date_creation,
        u.id AS auteur_id, u.nom, u.prenom, u.photo AS photo_utilisateur,
        (SELECT COUNT(*) FROM likes WHERE article_id = a.id AND type = 'like') AS nb_likes,
        (SELECT COUNT(*) FROM likes WHERE article_id = a.id AND type = 'dislike') AS nb_dislikes
    FROM articles a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    ORDER BY a.date_creation DESC
");

$articles = $requete->fetchAll(PDO::FETCH_ASSOC);

// Pour chaque article, on vérifie si l'utilisateur connecté a liké/disliké
if ($utilisateurId > 0) {
    foreach ($articles as &$article) {
        $likeReq = $pdo->prepare("SELECT type FROM likes WHERE utilisateur_id = ? AND article_id = ?");
        $likeReq->execute([$utilisateurId, $article["id"]]);
        $like = $likeReq->fetch();
        $article["mon_like"] = $like ? $like["type"] : null;
    }
}

echo json_encode(["statut" => 200, "articles" => $articles]);
?>
