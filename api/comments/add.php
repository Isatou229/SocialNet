<?php
/**
 * api/comments/add.php
 * Méthode : POST (JSON) { post_id, contenu }
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();
$data = get_json_body();

$postId = (int) ($data['post_id'] ?? 0);
$contenu = sanitize($data['contenu'] ?? '');

if (!$postId || !$contenu) {
    json_response(['success' => false, 'message' => 'post_id et contenu requis'], 400);
}

$stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, contenu, date_commentaire) VALUES (?, ?, ?, NOW())");
$stmt->execute([$postId, $user['id'], $contenu]);

$commentId = $pdo->lastInsertId();

json_response([
    'success' => true,
    'comment' => [
        'id' => $commentId,
        'post_id' => $postId,
        'contenu' => $contenu,
        'nom' => $user['nom'],
        'prenom' => $user['prenom'],
        'photo' => $user['photo'],
        'user_id' => $user['id'],
        'date_commentaire' => date('Y-m-d H:i:s')
    ]
]);
