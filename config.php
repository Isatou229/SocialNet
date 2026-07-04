<?php
/**
 * config.php
 * Connexion à la base de données MySQL via PDO.
 * Ce fichier est inclus par tous les scripts de l'API.
 */

// --- Paramètres de connexion (modifie si besoin) ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'socialnet');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Connexion à la base de données impossible : ' . $e->getMessage()
    ]);
    exit;
}

// Autorise les requêtes AJAX depuis la même origine (utile en dev)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
