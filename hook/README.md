# Guide d'Implémentation - Universal API

Ce document est le manuel de référence pour construire une API **"Digne des plus grands"**.
Il explique comment implémenter des fonctionnalités avancées (Auth, Web3, E-commerce) en utilisant le système de Hooks de l'Universal API.

Les exemples de code complets se trouvent dans les dossiers `hook/Controllers` et `hook/Middleware`.

---

## 1. Gestion des Utilisateurs (Auth & DB)

Une API moderne doit être **Stateless**. Cela signifie que nous n'utilisons pas de sessions PHP classiques (`$_SESSION`), mais des **Tokens** stockés en base de données ou signés (JWT).

### A. Inscription (Register)
**Fichier :** `hook/Controllers/AuthController.php` -> `register()`

Le processus est le suivant :
1.  Recevoir les données JSON (`username`, `password`).
2.  Vérifier si l'utilisateur existe déjà en Base de Données (MySQL/MariaDB).
3.  Hacher le mot de passe avec `password_hash()` (Sécurité indispensable).
4.  Insérer le nouvel utilisateur.

```sql
-- Structure SQL recommandée (voir database/schema.mysql.sql)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    password_hash VARCHAR(255),
    session_token VARCHAR(255) NULL
);
```

### B. Connexion (Login)
**Fichier :** `hook/Controllers/AuthController.php` -> `login()`

1.  Vérifier le couple user/pass (`password_verify`).
2.  Générer un **Session Token** unique (ex: `bin2hex(random_bytes(32))`).
3.  Stocker ce token en base de données associé à l'utilisateur.
4.  Retourner ce token au client JSON.

### C. Profil Utilisateur (/user/me)
**Fichier :** `hook/Controllers/AuthController.php` -> `me()`

Pour sécuriser cet endpoint, nous utilisons un **Middleware**.
Le client doit envoyer le token dans le header : `Authorization: Bearer <token>`.

Le Middleware (`hook/Middleware/AuthMiddleware.php`) :
1.  Récupère le header.
2.  Vérifie en base de données si le token existe.
3.  Si valide, laisse passer la requête. Sinon, renvoie 401 Unauthorized.

---

## 2. Fonctionnalités Web3

L'API Universelle est prête pour le futur du web.

### A. Crypto & Balance
**Fichier :** `hook/Controllers/Web3Controller.php` -> `balance()`

L'objectif est d'exposer les avoirs numériques de l'utilisateur.
*   Les données peuvent provenir de la BDD locale (`crypto_balance`) ou être récupérées via une API Blockchain externe.
*   Le format de réponse doit être clair et précis (JSON).

### B. Système de Localisation en Live
**Fichier :** `hook/Controllers/Web3Controller.php` -> `location()`

Pour des applications décentralisées (dApps) nécessitant une géolocalisation (ex: logistique, tracking) :
*   **POST** : Met à jour les coordonnées (Lat/Lon) de l'utilisateur authentifié.
*   **GET** : Récupère la dernière position connue.

Cela démontre la capacité de l'API à gérer des mises à jour d'état rapides via des requêtes légères.

---

## 3. Créer une Boutique (E-Commerce)

Une API E-commerce doit être rapide et structurée.

**Fichier :** `hook/Controllers/ShopController.php` -> `products()`

### Structure des Données (JSON)
Le client attend des structures prévisibles. Nous renvoyons toujours du JSON strict.

**Exemple de réponse :**
```json
{
  "products": [
    {
      "id": 1,
      "name": "Bitcoin Miner S19",
      "price": "2500.00",
      "stock": 10
    },
    {
      "id": 2,
      "name": "Hardware Wallet",
      "price": "99.99",
      "stock": 50
    }
  ]
}
```

### Implémentation
1.  Créer une table `products` (id, name, price, stock).
2.  Le contrôleur interroge la base via `Database::getInstance()->query()`.
3.  `echo json_encode(...)` renvoie la donnée brute, rapide et compatible avec tous les clients (Web, Mobile, App).

---

## 4. Documentation API (La Clé du Succès)

Pour qu'une API soit utilisée par "n'importe qui", elle doit être ultra-documentée.

### Swagger / OpenAPI
L'API Universelle supporte la génération de documentation.
*   Utilisez des annotations claires dans vos contrôleurs.
*   Activez Swagger UI via `APP_DOCS_ENABLED=true`.

### Bonnes Pratiques Documentées ici :
1.  **Codes HTTP** : Utilisez les bons codes (200 OK, 201 Created, 400 Bad Request, 401 Unauthorized, 500 Server Error).
2.  **Format d'Erreur Standard** : `{ "error": "Message clair" }`.
3.  **Versioning** : Préfixez vos routes (ex: `/api/v1/...`) pour ne pas casser les clients existants lors des mises à jour.

---

**Bastivan Consulting** - L'excellence par le code.
