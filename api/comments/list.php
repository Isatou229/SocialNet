<?php
/**
 * api/comments/list.php
 * Méthode : GET ?post_id=
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

require_auth();

$postId = (int) ($_GET['post_id'] ?? 0);
if (!$postId) {
    json_response(['success' => false, 'message' => 'post_id requis'], 400);
}

$stmt = $pdo->prepare(
    "SELECT c.id, c.contenu, c.date_commentaire, u.id AS user_id, u.nom, u.prenom, u.photo
     FROM comments c
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = ?
     ORDER BY c.date_commentaire ASC"
);
$stmt->execute([$postId]);

json_response(['success' => true, 'comments' => $stmt->fetchAll()]);
