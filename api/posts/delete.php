<?php
/**
 * api/posts/delete.php
 * Méthode : POST (JSON) { post_id }
 * Autorisé pour l'auteur de la publication, un modérateur ou un administrateur.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();
$data = get_json_body();
$postId = (int) ($data['post_id'] ?? 0);

if (!$postId) {
    json_response(['success' => false, 'message' => 'post_id requis'], 400);
}

$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    json_response(['success' => false, 'message' => 'Publication introuvable'], 404);
}

$estAuteur = $post['user_id'] == $user['id'];
$estModoOuAdmin = in_array($user['role'], ['moderateur', 'admin'], true);

if (!$estAuteur && !$estModoOuAdmin) {
    json_response(['success' => false, 'message' => 'Action non autorisée'], 403);
}

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$postId]);

json_response(['success' => true, 'message' => 'Publication supprimée']);
