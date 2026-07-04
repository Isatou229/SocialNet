<?php
/**
 * api/auth/reset-password.php
 * Méthode : POST (JSON) { token, mot_de_passe, confirmation }
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

$data = get_json_body();
$token = $data['token'] ?? '';
$motDePasse = $data['mot_de_passe'] ?? '';
$confirmation = $data['confirmation'] ?? '';

if (!$token || !$motDePasse || !$confirmation) {
    json_response(['success' => false, 'message' => 'Champs manquants'], 400);
}
if ($motDePasse !== $confirmation) {
    json_response(['success' => false, 'message' => 'Les mots de passe ne correspondent pas'], 400);
}
if (strlen($motDePasse) < 6) {
    json_response(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM tokens WHERE token = ? AND expiration > NOW()");
$stmt->execute([$token]);
$row = $stmt->fetch();

if (!$row) {
    json_response(['success' => false, 'message' => 'Lien invalide ou expiré'], 400);
}

$hash = password_hash($motDePasse, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
$stmt->execute([$hash, $row['user_id']]);

// Le token ne doit servir qu'une seule fois
$stmt = $pdo->prepare("DELETE FROM tokens WHERE id = ?");
$stmt->execute([$row['id']]);

json_response(['success' => true, 'message' => 'Mot de passe réinitialisé. Tu peux te connecter.']);
