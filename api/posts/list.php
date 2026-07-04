<?php
/**
 * api/posts/list.php
 * Retourne le fil d'actualité (toutes les publications, plus récentes en premier),
 * ou les publications d'un utilisateur précis avec ?user_id=
 * Méthode : GET, header Authorization: Bearer <token>
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$currentUser = require_auth();

$userIdFilter = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;

$sql = "
    SELECT p.id, p.contenu, p.image, p.date_publication,
           u.id AS auteur_id, u.nom, u.prenom, u.photo,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND type = 'like') AS nb_likes,
           (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND type = 'dislike') AS nb_dislikes,
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS nb_comments,
           (SELECT type FROM likes WHERE post_id = p.id AND user_id = :current_user) AS ma_reaction
    FROM posts p
    JOIN users u ON u.id = p.user_id
";

$params = [':current_user' => $currentUser['id']];

if ($userIdFilter) {
    $sql .= " WHERE p.user_id = :user_id ";
    $params[':user_id'] = $userIdFilter;
}

$sql .= " ORDER BY p.date_publication DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

json_response(['success' => true, 'posts' => $posts]);
