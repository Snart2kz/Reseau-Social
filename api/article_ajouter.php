<?php
// ==========================
// API : AJOUTER UN ARTICLE
// Crée une nouvelle publication
// ==========================

require_once "config.php";

$utilisateurId = $_POST["utilisateur_id"] ?? 0;
$description = $_POST["description"] ?? "";

if (empty($description)) {
    echo json_encode(["statut" => 400, "message" => "La description ne peut pas être vide."]);
    exit;
}

// Gestion de l'image
$image = null;
if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
    $extensions = ["jpg", "jpeg", "png", "gif"];
    $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    if (in_array($extension, $extensions)) {
        $nomImage = uniqid() . "." . $extension;
        move_uploaded_file($_FILES["image"]["tmp_name"], "../assets/images/" . $nomImage);
        $image = "assets/images/" . $nomImage;
    }
}

// Insertion dans la base de données
$requete = $pdo->prepare("INSERT INTO articles (utilisateur_id, description, image) VALUES (?, ?, ?)");
$requete->execute([$utilisateurId, $description, $image]);

echo json_encode(["statut" => 200, "message" => "Article publié avec succès."]);
?>
