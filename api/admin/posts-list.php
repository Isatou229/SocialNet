<?php
/**
 * api/admin/posts-list.php
 * Méthode : GET
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

require_role(['admin', 'moderateur']);

$stmt = $pdo->query(
    "SELECT p.id, p.contenu, p.date_publication, u.nom, u.prenom
     FROM posts p JOIN users u ON u.id = p.user_id
     ORDER BY p.date_publication DESC LIMIT 200"
);

json_response(['success' => true, 'posts' => $stmt->fetchAll()]);
