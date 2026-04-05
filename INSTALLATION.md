# 🎯 SMART BOOKING - Guide d'Installation

Application web de réservation de services beauté

## 📋 Prérequis

- XAMPP (PHP 7.4+, MySQL 5.7+)
- Navigateur web moderne

## 🚀 Installation

### Étape 1 : Copier les fichiers

1. Copiez le dossier `smart-booking` dans `C:\xampp\htdocs\`

### Étape 2 : Démarrer XAMPP

1. Ouvrez le **XAMPP Control Panel**
2. Démarrez **Apache** et **MySQL**

### Étape 3 : Créer la base de données

1. Ouvrez votre navigateur
2. Allez sur : `http://localhost/phpmyadmin`
3. Cliquez sur l'onglet **"SQL"**
4. Copiez tout le contenu du fichier `database_clean.sql`
5. Collez-le dans la zone SQL
6. Cliquez sur **"Exécuter"**

### Étape 4 : Accéder à l'application

**Interface Client :**
```
http://localhost/smart-booking/
```

**Panel Admin :**
```
http://localhost/smart-booking/admin.php
Username: admin
Password: admin123
```

## ✅ Fonctionnalités à tester

### Interface Client
1. Choisir une catégorie de services
2. Sélectionner un ou plusieurs services
3. Remplir le formulaire de réservation
4. Vérifier la détection des doublons (réserver 2 fois de suite)
5. Confirmer la réservation

### Panel Admin
1. Se connecter avec les identifiants
2. Voir les statistiques en temps réel
3. Filtrer les réservations par statut
4. Confirmer une réservation
5. Annuler une réservation
6. Supprimer une réservation

## 🎨 Design

- Interface moderne et responsive
- Compatible mobile, tablette et desktop
- Dégradés violet-rose élégants
- Animations fluides

## 📊 Structure de la base de données

- **categories** : 3 catégories de services
- **services** : 11 services au total
- **reservations** : Table vide (prête pour les tests)
- **reservation_services** : Table de liaison vide

## 🔒 Sécurité

- Protection anti-doublons intelligente
- Validation côté client et serveur
- Hash des mots de passe recommandé pour production

## 📱 Contact

Pour toute question sur l'installation ou l'utilisation.

---
**Version** : 1.0
**Date** : Janvier 2026
```

## 📦 Checklist avant d'envoyer au prof

Assurez-vous que votre dossier contient :
```
smart-booking/
├── 📄 index.html
├── 📄 style.css
├── 📄 script.js
├── 📄 config.php
├── 📄 admin.php
├── 📄 database_clean.sql  ← BASE VIDE
├── 📄 INSTALLATION.md     ← INSTRUCTIONS
├── 📄 README.md            ← DOCUMENTATION
└── 📁 api/
    └── 📄 book.php