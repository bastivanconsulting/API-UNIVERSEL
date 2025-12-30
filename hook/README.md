# Documentation des Hooks - Universal API

Le dossier `hook/` est l'espace réservé aux développeurs pour étendre les fonctionnalités de l'API Universelle.
Tout le code métier spécifique à votre application doit résider ici pour garantir que le cœur (`src/Core`) reste intact et puisse être mis à jour sans conflit.

## 📂 Structure du Dossier

```text
hook/
├── Controllers/     # Vos contrôleurs (Logique métier)
├── Middleware/      # Vos middlewares personnalisés (Auth, Validation...)
├── Views/           # Vos vues (Templates HTML/PHP)
├── routes.php       # Définition de vos routes personnalisées
└── README.md        # Ce fichier
```

L'espace de nom (Namespace) racine pour ce dossier est : `Bastivan\UniversalApi\Hook`.

---

## 🚀 Ajouter une nouvelle Route

Les routes sont définies dans `hook/routes.php`. Vous avez accès à l'instance `$router`.

### Route Simple

```php
use Bastivan\UniversalApi\Hook\Controllers\MonController;

// GET /api/hello
$router->get('/api/hello', [MonController::class, 'index']);
```

### Route avec Paramètres (Regex)

Le routeur supporte les expressions régulières pour les paramètres dynamiques.

```php
// GET /api/users/42
$router->get('/api/users/([0-9]+)', [MonController::class, 'show']);
```

### Route avec Options Avancées (Cache, Sécurité, Middleware)

Le 3ème argument de la méthode de route accepte un tableau d'options.

```php
$router->get('/api/data', [MonController::class, 'data'], [
    'cache' => 60,              // Cache la réponse 60 secondes dans Redis
    'middleware' => [           // Exécute ces middlewares avant le contrôleur
        \Bastivan\UniversalApi\Hook\Middleware\MonAuthMiddleware::class
    ],
    'security' => [
        'allowed_ips' => ['127.0.0.1'], // Restriction IP spécifique à cette route
        'allowed_methods' => ['GET']    // Force GET même si la route est définie autrement
    ]
]);
```

---

## 🎮 Créer un Contrôleur

Les contrôleurs doivent être placés dans `hook/Controllers/`.

Exemple : `hook/Controllers/MonController.php`

```php
<?php

namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Core\Response; // Optionnel, pour le typage ou helpers

class MonController
{
    public function index()
    {
        // Retour JSON simple
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Bonjour depuis le Hook!']);
    }

    public function show($id)
    {
        echo json_encode(['user_id' => $id]);
    }
}
```

---

## 🛡 Créer un Middleware

Les middlewares permettent d'intercepter la requête avant qu'elle n'atteigne le contrôleur (ex: Authentification).
Ils doivent implémenter une méthode statique `handle(string $uri)`.

Exemple : `hook/Middleware/AdminMiddleware.php`

```php
<?php

namespace Bastivan\UniversalApi\Hook\Middleware;

class AdminMiddleware
{
    public static function handle(string $uri): void
    {
        // Vérification d'un header secret par exemple
        $headers = getallheaders();
        if (!isset($headers['X-Admin-Token']) || $headers['X-Admin-Token'] !== 'SECRET123') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès interdit']);
            exit; // Arrête l'exécution immédiatement
        }
    }
}
```

---

## 💾 Accès à la Base de Données

Utilisez la classe `Database` du Core pour interagir avec vos données.

```php
use Bastivan\UniversalApi\Core\Database;

class UserController
{
    public function list()
    {
        $db = Database::getInstance();

        // Pour SQL (MySQL/PostgreSQL)
        $stmt = $db->query("SELECT * FROM users LIMIT 10");
        $users = $stmt->fetchAll();

        // Pour MongoDB
        // $users = $db->getConnection()->users->find([], ['limit' => 10]);

        echo json_encode($users);
    }
}
```

---

## 🎨 Utiliser des Vues

Si vous servez du HTML, placez vos fichiers dans `hook/Views/`.

```php
public function page()
{
    // Inclure la vue simplement
    require __DIR__ . '/../Views/ma_page.php';
}
```

---

**Bastivan Consulting** - Universal API
