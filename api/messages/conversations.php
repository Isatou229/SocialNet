<?php
/**
 * api/messages/conversations.php
 * Méthode : GET
 * Retourne la liste des contacts avec qui l'utilisateur a échangé,
 * triés par dernier message envoyé/reçu (un seul message le plus récent par contact).
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$user = require_auth();

$stmt = $pdo->prepare(
    "SELECT u.id, u.nom, u.prenom, u.photo, lm.message AS dernier_message, lm.date_message AS derniere_date
     FROM (
        SELECT IF(sender_id = :me1, receiver_id, sender_id) AS other_id, MAX(id) AS last_id
        FROM messages
        WHERE sender_id = :me2 OR receiver_id = :me3
        GROUP BY other_id
     ) t
     JOIN messages lm ON lm.id = t.last_id
     JOIN users u ON u.id = t.other_id
     ORDER BY lm.date_message DESC"
);
$stmt->execute([':me1' => $user['id'], ':me2' => $user['id'], ':me3' => $user['id']]);

json_response(['success' => true, 'conversations' => $stmt->fetchAll()]);
