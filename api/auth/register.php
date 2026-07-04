<?php
/**
 * api/auth/register.php
 * Inscription d'un nouvel utilisateur.
 * Méthode : POST (JSON) { nom, prenom, email, mot_de_passe, confirmation }
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

$data = get_json_body();

$nom = sanitize($data['nom'] ?? '');
$prenom = sanitize($data['prenom'] ?? '');
$email = trim($data['email'] ?? '');
$motDePasse = $data['mot_de_passe'] ?? '';
$confirmation = $data['confirmation'] ?? '';

if (!$nom || !$prenom || !$email || !$motDePasse) {
    json_response(['success' => false, 'message' => 'Tous les champs sont obligatoires'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Adresse email invalide'], 400);
}
if (strlen($motDePasse) < 6) {
    json_response(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
}
if ($motDePasse !== $confirmation) {
    json_response(['success' => false, 'message' => 'Les mots de passe ne correspondent pas'], 400);
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_response(['success' => false, 'message' => 'Cet email est déjà utilisé'], 409);
}

$hash = password_hash($motDePasse, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (nom, prenom, email, mot_de_passe, role, date_creation)
     VALUES (?, ?, ?, ?, 'user', NOW())"
);
$stmt->execute([$nom, $prenom, $email, $hash]);

// Email HTML de bienvenue / confirmation
$contenu = "<p>Bonjour $prenom,</p><p>Ton compte SocialNet ESGIS a été créé avec succès. Tu peux dès maintenant te connecter et commencer à publier !</p>";
$html = build_email_template('Bienvenue sur SocialNet ESGIS 🎉', $contenu, 'Se connecter', '../../vues/clients/connexion.html');
send_email($email, 'Bienvenue sur SocialNet ESGIS', $html);

json_response(['success' => true, 'message' => 'Compte créé avec succès. Tu peux te connecter.']);
