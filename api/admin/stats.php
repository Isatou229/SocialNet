<?php
/**
 * api/admin/stats.php
 * Méthode : GET — réservé admin/modérateur
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

require_role(['admin', 'moderateur']);

$stats = [
    'utilisateurs' => (int) $pdo->query("SELECT COUNT(*) AS n FROM users")->fetch()['n'],
    'publications' => (int) $pdo->query("SELECT COUNT(*) AS n FROM posts")->fetch()['n'],
    'commentaires' => (int) $pdo->query("SELECT COUNT(*) AS n FROM comments")->fetch()['n'],
    'amities' => (int) $pdo->query("SELECT COUNT(*) AS n FROM friends WHERE status='accepted'")->fetch()['n'],
    'messages' => (int) $pdo->query("SELECT COUNT(*) AS n FROM messages")->fetch()['n'],
];

// Inscriptions par mois (12 derniers mois)
$stmt = $pdo->query(
    "SELECT DATE_FORMAT(date_creation, '%Y-%m') AS mois, COUNT(*) AS total
     FROM users
     WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
     GROUP BY mois ORDER BY mois ASC"
);
$inscriptionsParMois = $stmt->fetchAll();

// Publications par jour (14 derniers jours)
$stmt = $pdo->query(
    "SELECT DATE(date_publication) AS jour, COUNT(*) AS total
     FROM posts
     WHERE date_publication >= DATE_SUB(NOW(), INTERVAL 14 DAY)
     GROUP BY jour ORDER BY jour ASC"
);
$publicationsParJour = $stmt->fetchAll();

json_response([
    'success' => true,
    'stats' => $stats,
    'inscriptions_par_mois' => $inscriptionsParMois,
    'publications_par_jour' => $publicationsParJour
]);
