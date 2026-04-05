-- ============================================
-- BASE DE DONNÉES VIDE - SMART BOOKING
-- Pour installation propre et tests
-- ============================================

-- Supprimer la base si elle existe
DROP DATABASE IF EXISTS smart_booking;

-- Créer la base de données
CREATE DATABASE smart_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_booking;

-- Table des catégories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon_class VARCHAR(50),
    color_class VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des services
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration VARCHAR(20) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réservations
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    comments TEXT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de liaison services-réservations
CREATE TABLE reservation_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reservation_id INT NOT NULL,
    service_id INT NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    service_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données de base : Catégories
INSERT INTO categories (name, icon_class, color_class) VALUES
('Soins & Esthétique', 'flower', 'from-pink-400 to-rose-500'),
('Coiffure', 'scissors', 'from-purple-400 to-indigo-500'),
('Maquillage & Beauté', 'palette', 'from-amber-400 to-orange-500');

-- Données de base : Services
INSERT INTO services (category_id, name, description, duration, price) VALUES
-- Soins & Esthétique
(1, 'Soins capillaires', 'Traitement hydratant et revitalisant pour cheveux', '45min', 35.00),
(1, 'Soins du visage', 'Nettoyage, gommage et masque hydratant', '1h', 50.00),
(1, 'Manucure', 'Soin complet des mains et vernis', '30min', 25.00),
(1, 'Pédicure', 'Soin complet des pieds et vernis', '45min', 35.00),
-- Coiffure
(2, 'Coupe de cheveux', 'Coupe personnalisée selon votre style', '30min', 30.00),
(2, 'Coloration', 'Coloration complète ou mèches', '1h30', 65.00),
(2, 'Coiffage & brushing', 'Mise en forme et brushing professionnel', '45min', 35.00),
(2, 'Permanente', 'Permanente ou défrisage', '2h', 80.00),
-- Maquillage & Beauté
(3, 'Maquillage classique', 'Maquillage naturel pour tous les jours', '30min', 30.00),
(3, 'Maquillage de soirée', 'Maquillage sophistiqué pour événements', '45min', 45.00),
(3, 'Maquillage mariée', 'Maquillage complet avec essai', '1h30', 100.00);

-- Afficher un message de confirmation
SELECT 'Base de données créée avec succès - Prête pour les tests!' as Message;
SELECT COUNT(*) as 'Nombre de services' FROM services;