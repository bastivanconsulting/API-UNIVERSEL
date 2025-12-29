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
    *   Cache Redis intégré (Body, Headers, Status) avec support Auth/Cookie.
    *   Support des bases de données MariaDB et PostgreSQL (via PDO optimisé).
    *   Optimisation des routes statiques (O(1)).
*   **Sécurité & Réseau** :
    *   **CDN Ready** : Support complet Cloudflare, Gcore et Proxy Nginx (Trusted Proxy Resolver).
    *   Détection réelle des IPs clients.
    *   SSL/TLS Obligatoire (Bloque HTTP et vieux TLS).
    *   Protection CSRF pour les requêtes avec Cookies.
    *   Rate Limiting (Redis).
    *   Headers de sécurité stricts (HSTS, CSP, X-Content-Type-Options).
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
| `SECURITY_ALLOWED_METHODS` | Restrictions globales des méthodes (ex: `GET,POST`) |
| `SECURITY_ACCESS_STRATEGY` | `ALLOW_ALL` ou `WHITELIST` |

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
*   **SecurityMiddleware** : Vérifie SSL, nettoie les headers, protège contre le CSRF et valide les IPs (Cloudflare/Gcore).
*   **RateLimitMiddleware** : Protège contre le brute-force.
    *   **Restriction des Méthodes HTTP** : Réduction de la surface d'attaque en limitant les verbes autorisés (GET, POST, PUT, DELETE, PATCH).

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

L'API supporte les méthodes `GET`, `POST`, `PUT`, `DELETE` et `PATCH`.

Vous pouvez restreindre les méthodes autorisées globalement via `.env` ou spécifiquement par route.

#### Restriction Globale
Dans `.env` :
```ini
# Autoriser uniquement GET et POST (Rejette PUT, DELETE, PATCH, etc.)
SECURITY_ALLOWED_METHODS=GET,POST
```

#### Restriction par Route
Vous pouvez renforcer la sécurité en définissant explicitement les méthodes autorisées pour une route donnée via les options de sécurité.

```php
// Exemple : '/custom' accepte uniquement GET.
// Si une requête POST arrive sur cette route (même si définie ailleurs), elle sera rejetée par la politique de sécurité avec une erreur 405.
$router->get('/custom', [MyController::class, 'index'], [
    'security' => [
        'allowed_methods' => ['GET']
    ]
]);

// Exemple pour PUT
$router->put('/users/update', [MyController::class, 'update']);
```

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

Voici comment implémenter une authentification simple via Token sans toucher au Core, en utilisant le support Middleware du Router.

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
Créez `hook/Middleware/AuthMiddleware.php`. Le middleware doit implémenter une méthode statique `handle`.

```php
<?php

namespace Bastivan\UniversalApi\Hook\Middleware;

use Bastivan\UniversalApi\Core\Database;

class AuthMiddleware
{
    /**
     * Intercepte la requête et valide le token.
     * @param string $uri L'URI demandée (ex: /secure/dashboard)
     */
    public static function handle(string $uri): void
    {
        $headers = getallheaders();
        $token = null;

        $authHeader = $headers['Authorization'] ?? '';

        // Support "Bearer <token>"
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
             $token = $matches[1];
        } else {
             // Fallback: Use the whole header if no Bearer prefix
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

        // Si tout est OK, le code continue vers le contrôleur
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

#### C. Utilisation dans `hook/routes.php`
Déclarez le middleware dans le tableau d'options de la route (3ème argument).

```php
use Bastivan\UniversalApi\Hook\Controllers\SecureController;
use Bastivan\UniversalApi\Hook\Middleware\AuthMiddleware;

// Le middleware AuthMiddleware::handle() sera exécuté avant SecureController::dashboard
$router->get('/secure/dashboard', [SecureController::class, 'dashboard'], [
    'middleware' => [AuthMiddleware::class]
]);
```

#### D. Le Contrôleur
Dans votre contrôleur (`hook/Controllers/SecureController.php`), vous n'avez plus besoin d'appeler le middleware manuellement.

```php
namespace Bastivan\UniversalApi\Hook\Controllers;

class SecureController
{
    public function dashboard()
    {
        // Le code ici n'est atteint que si AuthMiddleware a validé le token
        echo json_encode(['message' => 'Bienvenue dans la zone sécurisée']);
    }
}
```

---

**Développé avec ❤️ par Bastivan Consulting**
