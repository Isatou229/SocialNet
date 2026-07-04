<?php
/**
 * api/admin/roles-update.php
 * Méthode : POST (JSON) { user_id, role }  -- role = 'user' | 'moderateur' | 'admin'
 * Réservé exclusivement à l'administrateur (un modérateur ne peut pas gérer les rôles).
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$current = require_role(['admin']);
$data = get_json_body();

$userId = (int) ($data['user_id'] ?? 0);
$role = $data['role'] ?? '';

if (!$userId || !in_array($role, ['user', 'moderateur', 'admin'], true)) {
    json_response(['success' => false, 'message' => 'user_id et role valides requis'], 400);
}
if ($userId == $current['id']) {
    json_response(['success' => false, 'message' => 'Tu ne peux pas modifier ton propre rôle'], 400);
}

$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$role, $userId]);

json_response(['success' => true, 'message' => 'Rôle mis à jour']);
