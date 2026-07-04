<?php
/**
 * api/admin/users-delete.php
 * Méthode : POST (JSON) { user_id }
 * Un modérateur peut supprimer un utilisateur classique uniquement.
 * Un administrateur peut supprimer n'importe qui (sauf lui-même).
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$current = require_role(['admin', 'moderateur']);
$data = get_json_body();
$userId = (int) ($data['user_id'] ?? 0);

if (!$userId) {
    json_response(['success' => false, 'message' => 'user_id requis'], 400);
}
if ($userId == $current['id']) {
    json_response(['success' => false, 'message' => 'Tu ne peux pas supprimer ton propre compte'], 400);
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$target = $stmt->fetch();

if (!$target) {
    json_response(['success' => false, 'message' => 'Utilisateur introuvable'], 404);
}
if ($current['role'] === 'moderateur' && $target['role'] !== 'user') {
    json_response(['success' => false, 'message' => 'Un modérateur ne peut supprimer que des comptes utilisateur'], 403);
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$userId]);

json_response(['success' => true, 'message' => 'Utilisateur supprimé']);
