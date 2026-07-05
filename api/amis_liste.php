<?php
// ==========================
// API : LISTE DES AMIS (POUR LE CHAT)
// Retourne la liste des amis pour la sidebar du chat
// ==========================

require_once "config.php";

$utilisateurId = $_GET["utilisateur_id"] ?? 0;

if (empty($utilisateurId)) {
    echo json_encode(["statut" => 400, "message" => "Utilisateur non spécifié."]);
    exit;
}

$requete = $pdo->prepare("
    SELECT u.id, u.nom, u.prenom, u.photo
    FROM demandes_amis d
    JOIN utilisateurs u ON (CASE WHEN d.expediteur_id = ? THEN d.destinataire_id ELSE d.expediteur_id END) = u.id
    WHERE (d.expediteur_id = ? OR d.destinataire_id = ?) AND d.statut = 'accepte'
");
$requete->execute([$utilisateurId, $utilisateurId, $utilisateurId]);
$amis = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["statut" => 200, "amis" => $amis]);
?>
