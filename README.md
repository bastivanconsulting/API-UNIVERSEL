# Universal API by Bastivan Consulting

Bienvenue sur la fondation de l'API Universelle développée par **Bastivan Consulting**.

Ce projet fournit une base robuste, sécurisée et extensible pour des APIs modernes en PHP 8.4+, respectant les standards stricts et les meilleures pratiques.

## 🚀 Fonctionnalités Clés

*   **Architecture MVC Simple** : Facile à comprendre, sans la lourdeur d'un framework massif.
*   **Haute Disponibilité (HA)** :
    *   Chargement de la configuration depuis S3.
    *   Stockage des logs sur S3 ou local.
    *   Support natif Docker & Kubernetes.
*   **Performance** :
    *   Support GZIP natif.
    *   Cache Redis intégré.
    *   Support des bases de données MariaDB et PostgreSQL (via PDO optimisé).
*   **Sécurité & Réseau** :
    *   **CDN Ready** : Support complet Cloudflare, Gcore et Proxy Nginx.
    *   Détection réelle des IPs clients.
    *   SSL/TLS Obligatoire (Bloque HTTP et vieux TLS).
    *   Rate Limiting (Redis).
*   **Mode Debug Complet** :
    *   Analyse détaillée (Taille des paquets, OS, IP, Memory, etc.).

## 📋 Prérequis

*   PHP 8.4 ou supérieur
*   Composer
*   Redis
*   MariaDB ou PostgreSQL

## 🛠 Installation

### 1. Local (Docker Compose)

C'est la méthode recommandée pour le développement.

```bash
git clone git@github.com:bastivanconsulting/TLB-APPS.git
cd universal-api
docker-compose up -d --build
```

L'API sera accessible sur `http://localhost:8080` (Le SSL est forcé en prod, mais le conteneur écoute sur 80 pour être derrière un proxy/LB).

### 2. Manuel

```bash
composer install
cp .env.example .env
# Configurer .env
php -S localhost:8000 -t public
```

## ⚙️ Configuration

La configuration se fait via le fichier `.env` pour le local, ou via un fichier JSON stocké sur S3 pour la production (activé via `CONFIG_S3_BUCKET`).

### Variables Importantes

| Variable | Description |
|----------|-------------|
| `APP_ENV` | `local` ou `production` |
| `APP_DEBUG` | `true` ou `false` (Active les métriques détaillées) |
| `DB_CONNECTION` | `mysql` ou `pgsql` |
| `LOG_DRIVER` | `local` ou `s3` |
| `CONFIG_S3_BUCKET` | Bucket S3 pour la config HA |

## 📦 Structure du Code

Le code est structuré selon le pattern MVC, développé par **Bastivan Consulting** :

*   `src/Core` : Le cœur du framework (Router, Database, Config, Middleware).
*   `src/Controllers` : Les contrôleurs de l'API.
*   `src/Models` : Les modèles de données.
*   `src/Views` : Les vues (HTML).
*   `public` : Point d'entrée (index.php).

## 🔒 Sécurité

Cette API implémente plusieurs couches de sécurité par défaut :
*   **Prepared Statements** : Toutes les requêtes SQL utilisent `PDO::prepare`.
*   **SecurityMiddleware** : Vérifie SSL et nettoie les headers.
*   **RateLimitMiddleware** : Protège contre le brute-force.
    *   **Restriction des Méthodes HTTP** : Réduction de la surface d'attaque en limitant les verbes autorisés (GET, POST, etc.).

## 🪝 Système de Hooks

Le système de Hooks (`hook/`) vous permet d'étendre l'API, d'ajouter des routes, des contrôleurs et des middlewares sans modifier le cœur (`src/Core`) de l'application.

Voici comment utiliser les fonctionnalités avancées dans `hook/routes.php`.

### 1. Activer/Désactiver le Cache sur une Route

Le cache stocke la réponse complète (Body, Headers, Status) dans Redis pour la durée spécifiée.

```php
use Bastivan\UniversalApi\Hook\Controllers\MyController;

// Activer le cache pour 60 secondes
$router->get('/mon-api/data', [MyController::class, 'index'], [
    'cache' => 60
]);

// Route sans cache (par défaut)
$router->get('/mon-api/live', [MyController::class, 'live']);
```

### 2. Restriction IP sur les Routes

Vous pouvez restreindre l'accès à certaines routes uniquement à une liste d'IPs (WhiteList).

```php
// Accessible uniquement depuis ces IPs
$router->get('/admin/stats', [MyController::class, 'stats'], [
    'security' => [
        'allowed_ips' => ['192.168.1.50', '10.0.0.1']
    ]
]);
```

### 3. Limiter les types de méthodes HTTP (Surface d'Attaque)

Pour réduire les risques, vous pouvez définir globalement les méthodes HTTP autorisées dans votre fichier `.env`. Toute méthode non listée sera rejetée avec une erreur 405.

Dans `.env` :
```ini
# Autoriser uniquement GET et POST (Rejette PUT, DELETE, PATCH, etc.)
SECURITY_ALLOWED_METHODS=GET,POST
```

Cela s'applique à l'ensemble de l'API. C'est idéal pour durcir une API qui ne fait que de la lecture (GET) ou de la soumission simple (POST).

### 4. Créer des Routes avec Base de Données

Utilisez la classe `Database` pour interagir avec MySQL/MariaDB ou PostgreSQL.

Exemple de contrôleur (`hook/Controllers/UserController.php`) :

```php
namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Core\Database;

class UserController
{
    public function getUser($id)
    {
        $db = Database::getInstance();

        // Requête préparée (Sécurité Injection SQL)
        $stmt = $db->query("SELECT * FROM users WHERE id = ?", [$id]);
        $user = $stmt->fetch();

        if ($user) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    }
}
```

Dans `hook/routes.php` :
```php
$router->get('/users/([0-9]+)', [Bastivan\UniversalApi\Hook\Controllers\UserController::class, 'getUser']);
```

### 5. Créer un Système d'Authentification (Token)

Voici comment implémenter une authentification simple via Token sans toucher au Core.

#### A. La Table SQL
Créez une table `api_tokens` :
```sql
CREATE TABLE api_tokens (
    token VARCHAR(64) PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### B. Le Middleware d'Authentification
Créez `hook/Middleware/AuthMiddleware.php` :

```php
<?php

namespace Bastivan\UniversalApi\Hook\Middleware;

use Bastivan\UniversalApi\Core\Database;

class AuthMiddleware
{
    public static function check(): void
    {
        $headers = getallheaders();
        $token = null;

        $authHeader = $headers['Authorization'] ?? '';

        // Support "Bearer <token>"
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
             $token = $matches[1];
        } else {
             // Fallback: Use the whole header if no Bearer prefix (or handle as needed)
             $token = $authHeader ?: null;
        }

        if (!$token) {
            self::abort();
        }

        $db = Database::getInstance();
        $stmt = $db->query("SELECT user_id FROM api_tokens WHERE token = ?", [$token]);

        if (!$stmt->fetch()) {
            self::abort();
        }
    }

    private static function abort(): void
    {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => '403 Forbidden - Invalid or missing token']);
        exit;
    }
}
```

#### C. Utilisation dans le Contrôleur
Dans votre contrôleur (`hook/Controllers/SecureController.php`) :

```php
namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Hook\Middleware\AuthMiddleware;

class SecureController
{
    public function dashboard()
    {
        // Appeler le middleware manuellement au début de la méthode
        AuthMiddleware::check();

        echo json_encode(['message' => 'Bienvenue dans la zone sécurisée']);
    }
}
```

*Note : Une future mise à jour permettra de passer le Middleware dans les options du routeur.*

---

**Développé avec ❤️ par Bastivan Consulting**
