# Guides d'Implémentation : Web3 & E-Commerce

Ce document détaille comment implémenter des fonctionnalités métier avancées en utilisant l'Universal API.

---

## 1. Web3 : Crypto & Localisation

L'objectif est de créer des endpoints réactifs pour des applications décentralisées (dApps).

### A. Balance Crypto
**Scénario :** Retourner le solde Bitcoin de l'utilisateur authentifié.

```php
// Dans un contrôleur (ex: Web3Controller.php)
public function balance() {
    // 1. Récupérer l'utilisateur (via AuthMiddleware ou Token)
    // Supposons que $user contient les infos DB de l'utilisateur courant.

    // 2. Format de réponse standardisé
    echo json_encode([
        'network' => 'Bitcoin',
        'symbol' => 'BTC',
        'balance' => number_format($user['crypto_balance'], 8), // 8 décimales pour BTC
        'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' // Exemple
    ]);
}
```

### B. Système de Localisation en Live
**Scénario :** Permettre aux utilisateurs de partager leur position en temps réel (ex: Logistique, Uber-like).

**Route :** `POST /api/web3/location`

```php
public function updateLocation() {
    // 1. Lire le JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // 2. Validation stricte (Lat/Lon)
    if (!isset($input['lat']) || !isset($input['lon'])) {
        http_response_code(400); echo json_encode(['error' => 'Coordonnées manquantes']); return;
    }

    // 3. Mise à jour atomique en Base de Données
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE users SET location_lat = ?, location_lon = ? WHERE id = ?");
    $stmt->execute([$input['lat'], $input['lon'], $this->currentUserId]);

    echo json_encode(['status' => 'updated']);
}
```

*Astuce : Pour une performance maximale sur la localisation en temps réel, utilisez Redis (GeoAdd) au lieu de MySQL.*

---

## 2. Boutique en Ligne (Shop)

Une API E-commerce doit être capable de gérer un catalogue produits et des stocks.

### A. Structure JSON des Produits
Il est crucial de définir un schéma JSON clair pour que le Front-end (React/Vue) puisse l'afficher facilement.

**Exemple de réponse `/api/shop/products` :**

```json
{
  "products": [
    {
      "id": 101,
      "name": "Bitcoin Miner S19",
      "price": 2500.00,
      "currency": "USD",
      "stock": 10,
      "available": true
    },
    {
      "id": 102,
      "name": "Cold Wallet Ledger",
      "price": 79.99,
      "currency": "USD",
      "stock": 0,
      "available": false
    }
  ]
}
```

### B. Implémentation du Contrôleur (Catalogue)

```php
// ShopController.php

public function list() {
    $db = Database::getInstance()->getConnection();

    // Récupération optimisée (seulement les champs nécessaires)
    $stmt = $db->query("SELECT id, name, price, stock FROM products WHERE stock >= 0");
    $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Transformation des données (si nécessaire)
    foreach ($products as &$p) {
        $p['available'] = ($p['stock'] > 0);
    }

    echo json_encode(['products' => $products]);
}
```

### C. Gestion des Stocks (Achat)
Lors d'un achat, la décrémentation du stock doit être transactionnelle pour éviter de vendre un produit épuisé.

```php
public function buy($productId) {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    try {
        // 1. Vérifier le stock (FOR UPDATE verrouille la ligne)
        $stmt = $db->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product['stock'] <= 0) {
            throw new \Exception("Rupture de stock");
        }

        // 2. Décrémenter
        $update = $db->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
        $update->execute([$productId]);

        $db->commit();
        echo json_encode(['status' => 'Achat confirmé']);
    } catch (\Exception $e) {
        $db->rollBack();
        http_response_code(409);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
```
