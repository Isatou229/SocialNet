<?php
/**
 * api/messages/thread.php
 * Méthode : GET ?with=<user_id>
 * Retourne tous les messages échangés entre l'utilisateur connecté et ?with=
 * Le front-end appelle cette route toutes les 3 secondes (voir assets/js/chat.js)
 * pour simuler le temps réel sans Node.js (conforme à la consigne "intervalle JS").
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();
$withId = (int) ($_GET['with'] ?? 0);

if (!$withId) {
    json_response(['success' => false, 'message' => 'Paramètre with requis'], 400);
}

$stmt = $pdo->prepare(
    "SELECT id, sender_id, receiver_id, message, image, date_message
     FROM messages
     WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
     ORDER BY date_message ASC"
);
$stmt->execute([$user['id'], $withId, $withId, $user['id']]);

json_response(['success' => true, 'messages' => $stmt->fetchAll()]);
