<?php
/**
 * includes/auth_check.php
 * Comme la consigne impose une gestion de session via sessionStorage (JS) plutôt
 * que via $_SESSION PHP, l'authentification côté API se fait par un token :
 *  - généré et stocké dans la table `auth_tokens` à la connexion (api/auth/login.php)
 *  - renvoyé au client, qui le garde dans sessionStorage avec les infos user
 *  - renvoyé par le client dans le header "Authorization: Bearer <token>"
 *    à chaque appel AJAX (voir assets/js/common.js -> apiFetch)
 */

require_once __DIR__ . '/functions.php';

/** Récupère le token envoyé dans le header Authorization */
function get_bearer_token() {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authHeader = $headers['Authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
    if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Exige un utilisateur connecté. Retourne ses infos (tableau associatif `users`)
 * ou interrompt la requête avec une erreur 401 si le token est absent/invalide.
 */
function require_auth() {
    global $pdo;
    $token = get_bearer_token();
    if (!$token) {
        json_response(['success' => false, 'message' => 'Non authentifié'], 401);
    }
    $stmt = $pdo->prepare(
        "SELECT u.id, u.nom, u.prenom, u.email, u.photo, u.bio, u.role, u.date_creation
         FROM auth_tokens t
         JOIN users u ON u.id = t.user_id
         WHERE t.token = ?"
    );
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if (!$user) {
        json_response(['success' => false, 'message' => 'Session invalide ou expirée'], 401);
    }
    return $user;
}

/** Exige un utilisateur connecté ayant l'un des rôles donnés (ex: ['admin','moderateur']) */
function require_role($roles) {
    $user = require_auth();
    if (!in_array($user['role'], $roles, true)) {
        json_response(['success' => false, 'message' => 'Accès refusé : droits insuffisants'], 403);
    }
    return $user;
}
