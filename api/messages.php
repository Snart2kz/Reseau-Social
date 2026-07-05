<?php
// ==========================
// API : RÉCUPÉRER LES MESSAGES
// Retourne les messages entre deux utilisateurs
// ==========================

require_once "config.php";

$utilisateurId = $_GET["utilisateur_id"] ?? 0;
$destinataireId = $_GET["destinataire_id"] ?? 0;

if (empty($utilisateurId) || empty($destinataireId)) {
    echo json_encode(["statut" => 400, "message" => "Données manquantes."]);
    exit;
}

// Récupère les messages dans les deux sens
$requete = $pdo->prepare("
    SELECT m.id, m.contenu, m.image, m.date_envoi, m.expediteur_id, m.destinataire_id
    FROM messages m
    WHERE (m.expediteur_id = ? AND m.destinataire_id = ?)
       OR (m.expediteur_id = ? AND m.destinataire_id = ?)
    ORDER BY m.date_envoi ASC
");
$requete->execute([$utilisateurId, $destinataireId, $destinataireId, $utilisateurId]);
$messages = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["statut" => 200, "messages" => $messages]);
?>
