# Guide de Routing Avancé

Le routeur de l'Universal API est conçu pour la performance et la sécurité. Il permet de définir des comportements complexes (Cache, Sécurité, Middleware) directement dans la définition de la route.

## 1. Méthodes HTTP (GET, POST, PUT, DELETE)

Définir des routes est simple. Utilisez les méthodes de l'objet `$router`.

### GET (Lecture)
Idéal pour récupérer des données.
```php
$router->get('/api/products', [ProductController::class, 'list']);
```

### POST (Création)
Utilisé pour envoyer des données (formulaires, JSON).
```php
$router->post('/api/products', [ProductController::class, 'create']);
```

### PUT / PATCH (Mise à jour)
```php
$router->put('/api/products/([0-9]+)', [ProductController::class, 'update']);
```

### DELETE (Suppression)
```php
$router->delete('/api/products/([0-9]+)', [ProductController::class, 'delete']);
```

---

## 2. Système de Cache (Redis)

Vous pouvez mettre en cache la réponse complète d'une route pour soulager la base de données.
Le cache stocke le Body, les Headers et le Code Statut.

**Exemple : Cacher la liste des produits pendant 60 secondes.**

```php
$router->get('/api/products', [ProductController::class, 'list'], [
    'cache' => 60 // Durée en secondes
]);
```

*Note : Les routes authentifiées (avec header Authorization) sont cachées par utilisateur (le token est inclus dans la clé de cache), donc pas de risque de fuite de données entre utilisateurs.*

---

## 3. Sécurité et Limitation IP

Vous pouvez restreindre l'accès à une route spécifique à une liste d'adresses IP (Whitelisting). C'est crucial pour les endpoints d'administration ou les webhooks sensibles.

**Exemple : Route accessible uniquement depuis le bureau (IP fixe).**

```php
$router->post('/api/admin/deploy', [AdminController::class, 'deploy'], [
    'security' => [
        'allowed_ips' => ['192.168.1.50', '10.0.0.1'], // Seules ces IPs peuvent accéder
        'access_strategy' => 'WHITELIST'
    ]
]);
```

Si une IP non autorisée tente d'accéder, elle recevra une erreur `403 Forbidden`.

---

## 4. Limitation de Méthode (Method Limiting)

Bien que `get()` ou `post()` définissent la méthode, vous pouvez renforcer la sécurité en forçant explicitement les méthodes autorisées au niveau du middleware de sécurité.

```php
$router->match(['GET', 'POST'], '/api/versatile', [MyController::class, 'handle'], [
    'security' => [
        'allowed_methods' => ['GET', 'POST']
    ]
]);
```

---

## 5. Combinaison Complète

Voici une route "Digne des plus grands" : Cachée, Sécurisée par IP, et Authentifiée.

```php
use Bastivan\UniversalApi\Hook\Middleware\AuthMiddleware;

$router->get('/api/admin/stats', [AdminController::class, 'stats'], [
    'cache' => 300, // Cache 5 minutes
    'middleware' => [AuthMiddleware::class], // Authentification requise
    'security' => [
        'allowed_ips' => ['1.2.3.4'] // IP Admin uniquement
    ]
]);
```
