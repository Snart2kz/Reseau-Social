<?php
// ==========================
// API : DASHBOARD ADMIN
// Retourne les statistiques et les listes pour l'administration
// ==========================

require_once "config.php";

// Statistiques
$stats = [];
$stats["utilisateurs"] = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$stats["articles"] = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$stats["commentaires"] = $pdo->query("SELECT COUNT(*) FROM commentaires")->fetchColumn();
$stats["messages"] = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Liste des utilisateurs
$utilisateurs = $pdo->query("SELECT id, nom, prenom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC")->fetchAll(PDO::FETCH_ASSOC);

// Liste des articles
$articles = $pdo->query("
    SELECT a.id, a.description, a.date_creation, u.nom, u.prenom
    FROM articles a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    ORDER BY a.date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "statut" => 200,
    "stats" => $stats,
    "utilisateurs" => $utilisateurs,
    "articles" => $articles
]);
?>
