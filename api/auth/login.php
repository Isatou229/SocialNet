<?php
/**
 * api/auth/login.php
 * Connexion. Méthode : POST (JSON) { email, mot_de_passe }
 * Retourne les infos utilisateur + un token à stocker dans sessionStorage.
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

$data = get_json_body();
$email = trim($data['email'] ?? '');
$motDePasse = $data['mot_de_passe'] ?? '';

if (!$email || !$motDePasse) {
    json_response(['success' => false, 'message' => 'Email et mot de passe requis'], 400);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($motDePasse, $user['mot_de_passe'])) {
        json_response(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token) VALUES (?, ?)");
    $stmt->execute([$user['id'], $token]);

    unset($user['mot_de_passe']);

    json_response([
        'success' => true,
        'message' => 'Connexion réussie',
        'token' => $token,
        'user' => $user
    ]);
} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()], 500);
}
