<?php
// Désactiver l'affichage des erreurs en HTML
ini_set('display_errors', 0);
error_reporting(0);

// Headers JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Buffer output
ob_start();

try {
    // Inclure la configuration
    require_once '../config.php';
    
    // Vérifier la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }
    
    // Récupérer les données JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Données JSON invalides');
    }
    
    // Extraire les données
    $customer = $data['customer'] ?? [];
    $services = $data['services'] ?? [];
    $totalPrice = $data['totalPrice'] ?? 0;
    
    // Validation
    if (empty($customer['name'])) {
        throw new Exception('Le nom est requis');
    }
    
    if (empty($customer['email']) || !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email invalide');
    }
    
    if (empty($customer['phone'])) {
        throw new Exception('Téléphone requis');
    }
    
    if (empty($services)) {
        throw new Exception('Aucun service sélectionné');
    }
    
    // Nettoyer les données
    $customerName = htmlspecialchars(trim($customer['name']), ENT_QUOTES, 'UTF-8');
    $customerEmail = strtolower(htmlspecialchars(trim($customer['email']), ENT_QUOTES, 'UTF-8'));
    $customerPhone = preg_replace('/\s+/', '', htmlspecialchars(trim($customer['phone']), ENT_QUOTES, 'UTF-8'));
    $customerComments = htmlspecialchars(trim($customer['comments'] ?? ''), ENT_QUOTES, 'UTF-8');
    
    // ============================================
    // LOGIQUE ANTI-DOUBLONS
    // ============================================
    
    // Vérifier si une réservation similaire existe dans les dernières 24 heures
    $checkDuplicate = $pdo->prepare("
        SELECT id, created_at 
        FROM reservations 
        WHERE customer_email = ? 
        AND customer_phone = ? 
        AND status != 'cancelled'
        AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY created_at DESC
        LIMIT 1
    ");
    
    $checkDuplicate->execute([$customerEmail, $customerPhone]);
    $existingReservation = $checkDuplicate->fetch();
    
    if ($existingReservation) {
        // Calculer le temps écoulé
        $createdAt = new DateTime($existingReservation['created_at']);
        $now = new DateTime();
        $interval = $now->diff($createdAt);
        
        // Si moins de 5 minutes, c'est probablement un doublon accidentel
        if ($interval->i < 5 && $interval->h == 0 && $interval->d == 0) {
            ob_end_clean();
            http_response_code(409); // Conflict
            echo json_encode([
                'success' => false,
                'message' => 'Vous avez déjà effectué une réservation il y a quelques minutes. Veuillez vérifier vos emails ou patienter avant de réserver à nouveau.',
                'duplicate' => true,
                'reservation_id' => $existingReservation['id']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Si moins de 24h mais plus de 5 minutes, vérifier si c'est exactement les mêmes services
        $existingServices = $pdo->prepare("
            SELECT service_id 
            FROM reservation_services 
            WHERE reservation_id = ?
            ORDER BY service_id
        ");
        $existingServices->execute([$existingReservation['id']]);
        $existingServiceIds = $existingServices->fetchAll(PDO::FETCH_COLUMN);
        
        $newServiceIds = array_map(function($s) { return $s['id']; }, $services);
        sort($newServiceIds);
        
        // Si exactement les mêmes services
        if ($existingServiceIds === $newServiceIds) {
            ob_end_clean();
            http_response_code(409); // Conflict
            echo json_encode([
                'success' => false,
                'message' => 'Vous avez déjà réservé ces mêmes services aujourd\'hui (Réservation #' . $existingReservation['id'] . '). Si vous souhaitez modifier votre réservation, veuillez nous contacter.',
                'duplicate' => true,
                'reservation_id' => $existingReservation['id']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    
    // ============================================
    // ENREGISTREMENT DE LA RÉSERVATION
    // ============================================
    
    // Commencer une transaction
    $pdo->beginTransaction();
    
    // Insérer la réservation
    $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (customer_name, customer_email, customer_phone, comments, total_price, status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([
        $customerName,
        $customerEmail,
        $customerPhone,
        $customerComments,
        $totalPrice
    ]);
    
    $reservationId = $pdo->lastInsertId();
    
    // Insérer les services
    $stmtService = $pdo->prepare("
        INSERT INTO reservation_services 
        (reservation_id, service_id, service_name, service_price) 
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($services as $service) {
        $stmtService->execute([
            $reservationId,
            $service['id'],
            htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8'),
            $service['price']
        ]);
    }
    
    // Valider la transaction
    $pdo->commit();
    
    // Nettoyer le buffer
    ob_end_clean();
    
    // Envoyer la réponse JSON
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Réservation confirmée avec succès !',
        'reservationId' => $reservationId,
        'customer_name' => $customerName
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Annuler la transaction si nécessaire
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Nettoyer le buffer
    ob_end_clean();
    
    // Envoyer l'erreur en JSON
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>