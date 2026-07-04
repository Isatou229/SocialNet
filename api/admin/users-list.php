<?php
/**
 * api/admin/users-list.php
 * Méthode : GET ?q=  (recherche optionnelle)
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

require_role(['admin', 'moderateur']);

$q = trim($_GET['q'] ?? '');
$sql = "SELECT id, nom, prenom, email, role, photo, date_creation FROM users";
$params = [];

if ($q !== '') {
    $sql .= " WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?";
    $params = ["%$q%", "%$q%", "%$q%"];
}
$sql .= " ORDER BY date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

json_response(['success' => true, 'users' => $stmt->fetchAll()]);
