<?php
/**
 * api/admin/posts-delete.php
 * Méthode : POST (JSON) { post_id }
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

require_role(['admin', 'moderateur']);
$data = get_json_body();
$postId = (int) ($data['post_id'] ?? 0);

if (!$postId) {
    json_response(['success' => false, 'message' => 'post_id requis'], 400);
}

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$postId]);

json_response(['success' => true, 'message' => 'Publication supprimée']);
