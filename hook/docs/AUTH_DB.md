# Gestion des Utilisateurs (Auth & DB)

Ce guide explique comment implémenter un système d'authentification complet et sécurisé : Inscription, Connexion et Gestion de Session via Token.

## 1. Base de Données (SQL)

Nous utilisons une table `users` standard. Le champ `session_token` est la clé de notre système "Stateless".

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    session_token VARCHAR(255) NULL,
    crypto_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 2. Le Contrôleur (AuthController.php)

Ce contrôleur gère la logique métier. Il est situé dans `hook/Controllers/AuthController.php`.

### Inscription (Register)

```php
public function register()
{
    // 1. Récupération des données
    $data = json_decode(file_get_contents('php://input'), true);

    // 2. Validation
    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400); echo json_encode(['error' => 'Champs manquants']); return;
    }

    $db = Database::getInstance()->getConnection();

    // 3. Hashage du mot de passe (Argon2 ou Bcrypt via PASSWORD_DEFAULT)
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);

    // 4. Insertion sécurisée (PDO Prepared Statement)
    $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    try {
        $stmt->execute([$data['username'], $hash]);
        http_response_code(201);
        echo json_encode(['message' => 'Utilisateur créé']);
    } catch (\PDOException $e) {
        http_response_code(409); // Conflit (ex: username déjà pris)
        echo json_encode(['error' => 'Utilisateur existant']);
    }
}
```

### Connexion (Login)

```php
public function login()
{
    $data = json_decode(file_get_contents('php://input'), true);
    $db = Database::getInstance()->getConnection();

    // 1. Recherche de l'utilisateur
    $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    // 2. Vérification du mot de passe
    if ($user && password_verify($data['password'], $user['password_hash'])) {
        // 3. Génération d'un Token sécurisé
        $token = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux

        // 4. Sauvegarde du token en base
        $update = $db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
        $update->execute([$token, $user['id']]);

        // 5. Retour au client
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Identifiants invalides']);
    }
}
```

## 3. Middleware d'Authentification

Pour protéger les routes (ex: `/user/me`), on utilise un Middleware qui vérifie le Token.
Fichier : `hook/Middleware/AuthMiddleware.php`

```php
namespace Bastivan\UniversalApi\Hook\Middleware;

use Bastivan\UniversalApi\Core\Database;

class AuthMiddleware
{
    public static function handle(string $uri): void
    {
        // 1. Récupérer le Header Authorization
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';

        // 2. Extraire le token (Format "Bearer <token>")
        if (!str_starts_with($auth, 'Bearer ')) {
            self::abort();
        }
        $token = substr($auth, 7);

        // 3. Vérifier en base de données
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE session_token = ?");
        $stmt->execute([$token]);

        if (!$stmt->fetch()) {
            self::abort();
        }

        // Si tout est OK, le script continue vers le Contrôleur.
    }

    private static function abort()
    {
        http_response_code(401);
        echo json_encode(['error' => 'Non autorisé']);
        exit;
    }
}
```

## 4. Application dans les Routes

Enfin, on relie le tout dans `hook/routes.php`.

```php
// Routes Publiques
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);

// Route Protégée
$router->get('/api/user/me', [AuthController::class, 'me'], [
    'middleware' => [AuthMiddleware::class]
]);
```
