<?php
/**
 * api/auth/logout.php
 * Déconnexion. Supprime le token côté serveur.
 * Méthode : POST, header Authorization: Bearer <token>
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$token = get_bearer_token();
if ($token) {
    $stmt = $pdo->prepare("DELETE FROM auth_tokens WHERE token = ?");
    $stmt->execute([$token]);
}

json_response(['success' => true, 'message' => 'Déconnecté']);
