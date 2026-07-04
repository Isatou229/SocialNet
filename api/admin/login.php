<?php
/**
 * api/admin/login.php
 * Méthode : POST (JSON) { email, mot_de_passe }
 * Identique à la connexion classique, mais refuse les comptes "user".
 */


require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

$data = get_json_body();
$email = trim($data['email'] ?? '');
$motDePasse = $data['mot_de_passe'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        json_response(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    if (!password_verify($motDePasse, $user['mot_de_passe'])) {
        json_response(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    if (!in_array($user['role'], ['admin', 'moderateur'], true)) {
        json_response(['success' => false, 'message' => 'Accès réservé aux modérateurs et administrateurs'], 403);
    }

    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token) VALUES (?, ?)");
    $stmt->execute([$user['id'], $token]);

    
    unset($user['mot_de_passe']);

   json_response(['success' => true, 'token' => $token, 'user' => $user]);
} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Erreur de base de données. Vérifiez que la base SocialNet a bien été initialisée.'], 500);
}
