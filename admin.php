<?php
/**
 * Panel d'administration moderne - Smart Booking
 */

require_once 'config.php';

// Authentification
session_start();

$validUsername = 'admin';
$validPassword = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $validUsername && $_POST['password'] === $validPassword) {
        $_SESSION['admin_logged_in'] = true;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Actions admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    if (isset($_POST['change_status'])) {
        $reservationId = $_POST['reservation_id'];
        $newStatus = $_POST['new_status'];
        
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $reservationId]);
        
        $message = "Statut mis à jour avec succès";
    }
    
    if (isset($_POST['delete_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$reservationId]);
        
        $message = "Réservation supprimée avec succès";
    }
    
    $filter = $_GET['filter'] ?? 'all';
    $query = "
        SELECT r.*, 
               GROUP_CONCAT(rs.service_name SEPARATOR ', ') as services
        FROM reservations r
        LEFT JOIN reservation_services rs ON r.id = rs.reservation_id
    ";
    
    if ($filter !== 'all') {
        $query .= " WHERE r.status = ?";
    }
    
    $query .= " GROUP BY r.id ORDER BY r.created_at DESC";
    
    $stmt = $filter !== 'all' 
        ? $pdo->prepare($query) 
        : $pdo->query($query);
    
    if ($filter !== 'all') {
        $stmt->execute([$filter]);
    }
    
    $reservations = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Smart Booking</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --purple-50: #faf5ff;
            --purple-100: #f3e8ff;
            --purple-500: #a855f7;
            --purple-600: #9333ea;
            --pink-500: #ec4899;
            --pink-600: #db2777;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --red-500: #ef4444;
            --red-600: #dc2626;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #faf5ff 0%, #fce7f3 50%, #dbeafe 100%);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        /* Login Page */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 20px 60px rgba(168, 85, 247, 0.2);
            width: 100%;
            max-width: 420px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(168, 85, 247, 0.3);
        }
        
        .login-logo svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        .login-title {
            font-size: 1.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-600), var(--pink-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: var(--gray-700);
            font-size: 0.9375rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--purple-500);
            box-shadow: 0 0 0 4px rgba(168, 85, 247, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 85, 247, 0.4);
        }
        
        /* Dashboard */
        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Header */
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-icon svg {
            width: 28px;
            height: 28px;
            color: white;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
        }
        
        .header-subtitle {
            font-size: 0.875rem;
            color: var(--gray-700);
        }
        
        .logout-btn {
            padding: 0.75rem 1.5rem;
            background: var(--gray-100);
            color: var(--gray-700);
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: var(--gray-200);
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            border-radius: 50%;
        }
        
        .stat-card:nth-child(1)::before {
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
        }
        
        .stat-card:nth-child(2)::before {
            background: linear-gradient(135deg, #fbbf24, #f97316);
        }
        
        .stat-card:nth-child(3)::before {
            background: linear-gradient(135deg, var(--emerald-500), #14b8a6);
        }
        
        .stat-card:nth-child(4)::before {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-700);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--purple-600), var(--pink-600));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.15;
        }
        
        /* Filters */
        .filters-card {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.625rem 1.25rem;
            border: 2px solid var(--gray-200);
            background: white;
            border-radius: 0.75rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--gray-700);
            font-size: 0.875rem;
        }
        
        .filter-btn:hover {
            border-color: var(--purple-500);
            color: var(--purple-600);
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);
        }
        
        /* Table Container */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0;
        }
        
        .table-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .table-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, var(--purple-500), var(--pink-500));
            color: white;
        }
        
        .table-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        th {
            background: var(--gray-50);
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 700;
            color: var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }
        
        td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-800);
            white-space: nowrap;
        }
        
        td.service-cell {
            max-width: 200px;
            white-space: normal;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background: var(--purple-50);
        }
        
        .reservation-id {
            font-weight: 700;
            color: var(--purple-600);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 1rem;
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.025em;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
        }
        
        .status-confirmed {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }
        
        .status-cancelled {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }
        
        .action-btns {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn-small {
            padding: 0.375rem 0.875rem;
            font-size: 0.8125rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-confirm {
            background: linear-gradient(135deg, var(--emerald-500), var(--emerald-600));
            color: white;
        }
        
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: white;
        }
        
        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, var(--red-500), var(--red-600));
            color: white;
        }
        
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        
        .message {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--emerald-500);
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-700);
        }
        
        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            opacity: 0.3;
        }
        
        /* Responsive Design Professionnel */
        @media (max-width: 1024px) {
            .dashboard {
                padding: 1.5rem;
            }
            
            table {
                min-width: 900px;
            }
            
            th, td {
                padding: 1rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 1.25rem;
            }
            
            .header-left {
                flex-direction: column;
            }
            
            .header-title {
                font-size: 1.25rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-value {
                font-size: 1.75rem;
            }
            
            .filters-card {
                padding: 1rem;
            }
            
            .filters {
                gap: 0.5rem;
            }
            
            .filter-btn {
                font-size: 0.8125rem;
                padding: 0.5rem 1rem;
            }
            
            .table-header {
                padding: 1.25rem 1.5rem;
            }
            
            .table-title {
                font-size: 1.125rem;
            }
            
            table {
                min-width: 800px;
            }
            
            th, td {
                padding: 0.875rem;
                font-size: 0.8125rem;
            }
            
            .btn-small {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
            }
            
            .action-btns {
                gap: 0.375rem;
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .table-header {
                padding: 1rem;
            }
            
            table {
                min-width: 700px;
            }
            
            th, td {
                padding: 0.75rem;
                font-size: 0.75rem;
            }
            
            .reservation-id {
                font-size: 0.875rem;
            }
        }
        
        /* Scroll indicator */
        .scroll-hint {
            display: none;
            text-align: center;
            padding: 0.75rem;
            background: var(--purple-50);
            color: var(--purple-600);
            font-size: 0.8125rem;
            font-weight: 600;
            border-bottom-left-radius: 1.5rem;
            border-bottom-right-radius: 1.5rem;
        }
        
        @media (max-width: 1024px) {
            .scroll-hint {
                display: block;
            }
        }
    </style>
</head>
<body>
    <?php if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true): ?>
        <!-- Login -->
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h2 class="login-title">Smart Booking</h2>
                    <p class="login-subtitle">Panel d'administration</p>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text" name="username" class="form-input" required autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    <button type="submit" name="login" class="btn-login">Se connecter</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Dashboard -->
        <div class="dashboard">
            <?php if (isset($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Header -->
            <div class="admin-header">
                <div class="header-left">
                    <div class="header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="header-title">Administration</h1>
                        <p class="header-subtitle">Gestion des réservations</p>
                    </div>
                </div>
                <a href="?logout" class="logout-btn">Déconnexion</a>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <?php
                $totalReservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
                $pendingCount = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
                $confirmedCount = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'")->fetchColumn();
                $totalRevenue = $pdo->query("SELECT SUM(total_price) FROM reservations WHERE status != 'cancelled'")->fetchColumn();
                ?>
                <div class="stat-card">
                    <div class="stat-label">Total Réservations</div>
                    <div class="stat-value"><?php echo $totalReservations; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">En Attente</div>
                    <div class="stat-value"><?php echo $pendingCount; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Confirmées</div>
                    <div class="stat-value"><?php echo $confirmedCount; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Revenu Total</div>
                    <div class="stat-value"><?php echo number_format($totalRevenue, 0); ?>€</div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters-card">
                <div class="filters">
                    <a href="?filter=all" class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">Toutes</a>
                    <a href="?filter=pending" class="filter-btn <?php echo $filter === 'pending' ? 'active' : ''; ?>">En attente</a>
                    <a href="?filter=confirmed" class="filter-btn <?php echo $filter === 'confirmed' ? 'active' : ''; ?>">Confirmées</a>
                    <a href="?filter=cancelled" class="filter-btn <?php echo $filter === 'cancelled' ? 'active' : ''; ?>">Annulées</a>
                </div>
            </div>
            
            <!-- Table -->
            <div class="table-card">
                <div class="table-header">
                    <h2 class="table-title">Liste des Réservations</h2>
                </div>
                
                <?php if (count($reservations) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Services</th>
                                <th>Prix</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td class="reservation-id">#<?php echo $reservation['id']; ?></td>
                                <td><?php echo htmlspecialchars($reservation['customer_name']); ?></td>
                                <td style="font-size: 0.8125rem;"><?php echo htmlspecialchars($reservation['customer_email']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['customer_phone']); ?></td>
                                <td class="service-cell"><?php echo htmlspecialchars($reservation['services']); ?></td>
                                <td style="font-weight: 700; color: var(--purple-600);"><?php echo $reservation['total_price']; ?>€</td>
                                <td style="font-size: 0.8125rem;"><?php echo date('d/m/Y H:i', strtotime($reservation['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $reservation['status']; ?>">
                                        <?php 
                                        $labels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée'];
                                        echo $labels[$reservation['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($reservation['status'] === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="new_status" value="confirmed">
                                            <button type="submit" name="change_status" class="btn-small btn-confirm">✓</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="new_status" value="cancelled">
                                            <button type="submit" name="change_status" class="btn-small btn-cancel">✕</button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette réservation ?');">
                                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                            <button type="submit" name="delete_reservation" class="btn-small btn-delete">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="scroll-hint">← Faites défiler horizontalement pour voir plus →</div>
                <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <h3 style="margin-bottom: 0.5rem; color: var(--gray-900);">Aucune réservation</h3>
                    <p>Les nouvelles réservations apparaîtront ici</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>