<?php
/**
 * api/likes/toggle.php
 * Méthode : POST (JSON) { post_id, type }  -- type = 'like' ou 'dislike'
 *
 * Règle : un utilisateur n'a qu'une réaction par publication.
 *  - Si la même réaction existe déjà -> on la retire (toggle off)
 *  - Si l'autre réaction existe -> on la remplace
 *  - Sinon -> on la crée
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();
$data = get_json_body();

$postId = (int) ($data['post_id'] ?? 0);
$type = $data['type'] ?? '';

if (!$postId || !in_array($type, ['like', 'dislike'], true)) {
    json_response(['success' => false, 'message' => 'post_id et type (like/dislike) requis'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$postId, $user['id']]);
$existing = $stmt->fetch();

if ($existing && $existing['type'] === $type) {
    // Même réaction -> on retire
    $stmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
    $stmt->execute([$existing['id']]);
    $maReaction = null;
} elseif ($existing) {
    // Réaction opposée -> on remplace
    $stmt = $pdo->prepare("UPDATE likes SET type = ? WHERE id = ?");
    $stmt->execute([$type, $existing['id']]);
    $maReaction = $type;
} else {
    // Aucune réaction -> on crée
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id, type) VALUES (?, ?, ?)");
    $stmt->execute([$user['id'], $postId, $type]);
    $maReaction = $type;
}

$stmt = $pdo->prepare("SELECT
    (SELECT COUNT(*) FROM likes WHERE post_id = ? AND type = 'like') AS nb_likes,
    (SELECT COUNT(*) FROM likes WHERE post_id = ? AND type = 'dislike') AS nb_dislikes
");
$stmt->execute([$postId, $postId]);
$counts = $stmt->fetch();

json_response([
    'success' => true,
    'ma_reaction' => $maReaction,
    'nb_likes' => (int) $counts['nb_likes'],
    'nb_dislikes' => (int) $counts['nb_dislikes']
]);
