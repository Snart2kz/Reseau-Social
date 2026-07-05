<?php
// ==========================
// API : AMIS
// Retourne les demandes reçues, les amis, et les utilisateurs
// ==========================

require_once "config.php";

$utilisateurId = $_GET["utilisateur_id"] ?? 0;

if (empty($utilisateurId)) {
    echo json_encode(["statut" => 400, "message" => "Utilisateur non spécifié."]);
    exit;
}

// 1. Demandes d'amitié reçues (en attente)
$demandesRecues = $pdo->prepare("
    SELECT d.id, u.id AS expediteur_id, u.nom, u.prenom, u.photo
    FROM demandes_amis d
    JOIN utilisateurs u ON d.expediteur_id = u.id
    WHERE d.destinataire_id = ? AND d.statut = 'en_attente'
");
$demandesRecues->execute([$utilisateurId]);
$demandes = $demandesRecues->fetchAll(PDO::FETCH_ASSOC);

// 2. Liste des amis (demandes acceptées)
$amis = $pdo->prepare("
    SELECT u.id, u.nom, u.prenom, u.photo
    FROM demandes_amis d
    JOIN utilisateurs u ON (CASE WHEN d.expediteur_id = ? THEN d.destinataire_id ELSE d.expediteur_id END) = u.id
    WHERE (d.expediteur_id = ? OR d.destinataire_id = ?) AND d.statut = 'accepte'
");
$amis->execute([$utilisateurId, $utilisateurId, $utilisateurId]);
$listeAmis = $amis->fetchAll(PDO::FETCH_ASSOC);

// 3. Liste des IDs des amis (pour exclusion)
$idsAmis = [$utilisateurId];
foreach ($listeAmis as $ami) {
    $idsAmis[] = $ami["id"];
}

// 4. Utilisateurs que l'utilisateur peut ajouter (pas encore amis)
$placeholders = implode(",", array_fill(0, count($idsAmis), "?"));
$autres = $pdo->prepare("
    SELECT id, nom, prenom, photo
    FROM utilisateurs
    WHERE id NOT IN ($placeholders)
    ORDER BY nom ASC
");
$autres->execute($idsAmis);
$utilisateurs = $autres->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "statut" => 200,
    "demandes_recues" => $demandes,
    "amis" => $listeAmis,
    "utilisateurs" => $utilisateurs
]);
?>
