<?php
/**
 * Configuration de la base de données
 */

// Paramètres de connexion pour XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration email
define('ADMIN_EMAIL', 'admin@smart-booking.com');

// Connexion à la base de données
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log l'erreur
    error_log("Erreur de connexion à la base de données: " . $e->getMessage());
    
    // Retourner une erreur JSON si appelé depuis l'API
    if (strpos($_SERVER['REQUEST_URI'], 'api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données'
        ]);
        exit;
    }
    
    die("Erreur de connexion à la base de données");
}

// Fonction pour nettoyer les entrées
function cleanInput($data) {
    if ($data === null || $data === '') {
        return '';
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


// Fonction pour valider l'email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fonction pour valider le téléphone
function validatePhone($phone) {
    $phone = preg_replace('/\s+/', '', $phone);
    return preg_match('/^[0-9]{10}$/', $phone);
}
