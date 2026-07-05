<?php
// ==========================
// API : MODIFIER LE PROFIL
// Met à jour les informations personnelles
// ==========================

require_once "config.php";

$id = $_POST["id"] ?? 0;
$nom = $_POST["nom"] ?? "";
$prenom = $_POST["prenom"] ?? "";
$email = $_POST["email"] ?? "";

if (empty($nom) || empty($prenom) || empty($email)) {
    echo json_encode(["statut" => 400, "message" => "Veuillez remplir tous les champs."]);
    exit;
}

// Gestion de la photo de profil
$photo = null;
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {
    $extensions = ["jpg", "jpeg", "png", "gif"];
    $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

    if (in_array($extension, $extensions)) {
        $nomPhoto = uniqid() . "." . $extension;
        move_uploaded_file($_FILES["photo"]["tmp_name"], "../assets/images/" . $nomPhoto);
        $photo = $nomPhoto;
    }
}

// Mise à jour dans la base de données
if ($photo) {
    $requete = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, photo = ? WHERE id = ?");
    $requete->execute([$nom, $prenom, $email, $photo, $id]);
} else {
    $requete = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id = ?");
    $requete->execute([$nom, $prenom, $email, $id]);
}

echo json_encode(["statut" => 200, "message" => "Profil mis à jour avec succès."]);
?>
