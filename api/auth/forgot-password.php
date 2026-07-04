<?php
/**
 * api/auth/forgot-password.php
 * Méthode : POST (JSON) { email }
 * Génère un token de réinitialisation (table `tokens`) et envoie un email HTML.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

$data = get_json_body();
$email = trim($data['email'] ?? '');

if (!$email) {
    json_response(['success' => false, 'message' => 'Email requis'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Réponse identique que l'utilisateur existe ou non (ne pas révéler les emails inscrits)
if (!$user) {
    json_response(['success' => true, 'message' => 'Si ce compte existe, un email a été envoyé.']);
}

$token = bin2hex(random_bytes(20));
$expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

$stmt = $pdo->prepare("INSERT INTO tokens (user_id, token, expiration) VALUES (?, ?, ?)");
$stmt->execute([$user['id'], $token, $expiration]);

$lien = "../../vues/clients/reinitialiser-mot-de-passe.html?token=$token";
$contenu = "<p>Bonjour {$user['prenom']},</p><p>Tu as demandé à réinitialiser ton mot de passe. Ce lien est valable 1 heure.</p>";
$html = build_email_template('Réinitialisation de mot de passe', $contenu, 'Réinitialiser mon mot de passe', $lien);
send_email($email, 'Réinitialisation de mot de passe — SocialNet ESGIS', $html);

json_response(['success' => true, 'message' => 'Si ce compte existe, un email a été envoyé.']);
