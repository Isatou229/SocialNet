<?php
/**
 * api/comments/delete.php
 * Méthode : POST (JSON) { comment_id }
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();
$data = get_json_body();
$commentId = (int) ($data['comment_id'] ?? 0);

if (!$commentId) {
    json_response(['success' => false, 'message' => 'comment_id requis'], 400);
}

$stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->execute([$commentId]);
$comment = $stmt->fetch();

if (!$comment) {
    json_response(['success' => false, 'message' => 'Commentaire introuvable'], 404);
}

$autorise = $comment['user_id'] == $user['id'] || in_array($user['role'], ['moderateur', 'admin'], true);
if (!$autorise) {
    json_response(['success' => false, 'message' => 'Action non autorisée'], 403);
}

$stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
$stmt->execute([$commentId]);

json_response(['success' => true, 'message' => 'Commentaire supprimé']);
