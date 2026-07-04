<?php
// ==========================
// API : MOT DE PASSE OUBLIÉ
// Envoie un email de réinitialisation
// ==========================

require_once "config.php";

$email = $_POST["email"] ?? "";

if (empty($email)) {
    echo json_encode(["statut" => 400, "message" => "Veuillez entrer votre email."]);
    exit;
}

// Vérifie si l'email existe
$verification = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$verification->execute([$email]);

if (!$verification->fetch()) {
    echo json_encode(["statut" => 404, "message" => "Aucun compte trouvé avec cet email."]);
    exit;
}

// Génère un token unique
$token = bin2hex(random_bytes(32));
$dateExpiration = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Enregistre le token dans la base de données
$requete = $pdo->prepare("
    INSERT INTO tokens_reset (email, token, date_expiration)
    VALUES (?, ?, ?)
");
$requete->execute([$email, $token, $dateExpiration]);

// Pour cet exposé, on simule l'envoi d'email
// Dans un vrai projet, on utiliserait mail() ou une bibliothèque
echo json_encode([
    "statut" => 200,
    "message" => "Un email de réinitialisation a été envoyé à $email. (Token : $token)"
]);
?>
