# 🎯 Smart Booking - Système de Réservation

Application web moderne de réservation de services beauté avec interface élégante et intuitive.

## 📋 Fonctionnalités

### Pour les Clients
- ✨ Interface moderne et responsive
- 🎨 3 catégories de services (Soins, Coiffure, Maquillage)
- 🛒 Sélection multiple de services
- 📝 Formulaire de réservation avec validation
- 📧 Email de confirmation automatique
- 💰 Calcul automatique du prix total

### Pour les Administrateurs
- 📊 Dashboard avec statistiques
- 📋 Gestion des réservations
- ✅ Confirmation/Annulation des réservations
- 🗑️ Suppression de réservations
- 🔍 Filtres par statut

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP PDO activée

### Étape 1 : Configuration de la base de données

1. Créez la base de données en important le fichier SQL :
```bash
mysql -u root -p < database.sql
```

Ou via phpMyAdmin :
- Créez une nouvelle base de données nommée `smart_booking`
- Importez le fichier `database.sql`

### Étape 2 : Configuration PHP

1. Modifiez le fichier `config.php` avec vos informations :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_booking');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

2. Configurez l'envoi d'emails (optionnel) :
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'votre-email@gmail.com');
define('SMTP_PASS', 'votre-mot-de-passe-app');
define('ADMIN_EMAIL', 'admin@smart-booking.com');
```

### Étape 3 : Structure des fichiers

Organisez vos fichiers comme suit :
```
smart-booking/
├── index.html
├── style.css
├── script.js
├── config.php
├── admin.php
├── database.sql
├── api/
│   └── book.php
└── README.md
```

### Étape 4 : Permissions

Assurez-vous que le serveur web a les permissions appropriées :
```bash
chmod 755 /var/www/html/smart-booking
chmod 644 /var/www/html/smart-booking/*.php
chmod 644 /var/www/html/smart-booking/api/*.php
```

## 🎮 Utilisation

### Interface Client

1. Accédez à `http://localhost/smart-booking/`
2. Choisissez une catégorie de services
3. Sélectionnez un ou plusieurs services
4. Remplissez le formulaire de contact
5. Vérifiez le récapitulatif
6. Confirmez votre réservation

### Panel Admin

1. Accédez à `http://localhost/smart-booking/admin.php`
2. Connectez-vous avec :
   - **Username:** `admin`
   - **Password:** `admin123`
3. Gérez les réservations depuis le dashboard

⚠️ **IMPORTANT** : Changez les identifiants admin en production !

## 🔒 Sécurité

### À faire avant la mise en production :

1. **Modifier les identifiants admin** dans `admin.php`
2. **Hasher le mot de passe** :
```php
$hashedPassword = password_hash('votre_nouveau_mdp', PASSWORD_BCRYPT);
```

3. **Activer HTTPS** sur votre serveur

4. **Configurer les headers de sécurité** dans votre `.htaccess` :
```apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

5. **Limiter les tentatives de connexion** au panel admin

6. **Utiliser des variables d'environnement** pour les informations sensibles

## 📧 Configuration Email

Pour l'envoi d'emails de confirmation, vous pouvez utiliser :

### Option 1 : Gmail
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'votre-email@gmail.com');
define('SMTP_PASS', 'votre-mot-de-passe-application');
```

⚠️ Activez "Accès moins sécurisé" ou utilisez un mot de passe d'application

### Option 2 : Service SMTP dédié
- SendGrid
- Mailgun
- Amazon SES

## 🎨 Personnalisation

### Modifier les couleurs
Éditez les variables CSS dans `style.css` :
```css
:root {
    --purple-500: #a855f7;
    --pink-500: #ec4899;
    /* Vos couleurs personnalisées */
}
```

### Ajouter des services
Modifiez le fichier `script.js` :
```javascript
const servicesData = {
    votre_categorie: {
        name: 'Votre Catégorie',
        services: [
            { id: 12, name: 'Nouveau service', ... }
        ]
    }
};
```

Ou ajoutez-les directement en base de données.

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez les identifiants dans `config.php`
- Assurez-vous que MySQL est démarré
- Vérifiez que l'extension PDO est activée

### Les emails ne sont pas envoyés
- Vérifiez la configuration SMTP
- Consultez les logs d'erreur PHP
- Testez avec `mail()` simple d'abord

### Erreur 404 sur l'API
- Vérifiez que le dossier `api/` existe
- Vérifiez les permissions des fichiers
- Activez `mod_rewrite` si nécessaire

## 📱 Responsive Design

L'application est entièrement responsive et s'adapte à :
- 📱 Smartphones (< 768px)
- 📱 Tablettes (768px - 1024px)
- 💻 Desktop (> 1024px)

## 🔄 Mises à jour futures

- [ ] Calendrier de disponibilités
- [ ] Paiement en ligne
- [ ] SMS de confirmation
- [ ] API REST complète
- [ ] Application mobile
- [ ] Multi-langues

## 📞 Support

Pour toute question ou problème :
- Consultez la documentation
- Vérifiez les logs d'erreur
- Ouvrez une issue sur GitHub

## 📄 Licence

Ce projet est sous licence MIT. Vous êtes libre de l'utiliser et de le modifier.

## 👨‍💻 Auteur

Smart Booking - 2026

---

**Bon développement ! 🚀**