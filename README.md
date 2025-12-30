# Universal API by Bastivan Consulting

Bienvenue sur la fondation de l'API Universelle développée par **Bastivan Consulting**.

Ce projet fournit une architecture robuste, sécurisée et extensible pour des APIs modernes en **PHP 8.4+**. Elle est conçue pour la haute disponibilité, la performance, et la sécurité dès la conception.

Cette API est pensée pour être **"Digne des Plus Grands"**, intégrant nativement des concepts avancés comme le Web3, le E-commerce temps réel et une sécurité militaire.

---

## 🚀 Fonctionnalités Clés

### 🏗 Architecture & Core
*   **Architecture MVC Légère** : Une structure claire sans la lourdeur des frameworks monolithiques.
*   **Multi-Base de Données** : Support natif et optimisé pour **MySQL/MariaDB**, **PostgreSQL**, **MongoDB** et **Redis**.
*   **Haute Disponibilité (HA)** : Support S3 pour config/logs, Endpoints de santé (`/status`).

### ⚡ Performance
*   **Moteur de Routing Hybride** : Optimisation O(1) / Regex.
*   **Caching Redis** : Cache complet des réponses et Rate Limiting atomique.
*   **Compression GZIP** : Activée par défaut.

### 🔒 Sécurité (Security by Design)
*   **Protection CSRF & Headers Stricts** (HSTS, CSP, etc.).
*   **IP Whitelisting & Trusted Proxies**.
*   **Prepared Statements** : 100% PDO pour éviter les injections SQL.

---

## 📚 Guide du Développeur : Comment faire une API d'Elite ?

Pour comprendre comment implémenter des fonctionnalités complexes (Auth, Web3, Shop), consultez la documentation détaillée dans le dossier `hook/`.

👉 **[LIRE LE GUIDE COMPLET D'IMPLÉMENTATION (HOOK/README.MD)](hook/README.md)**

Ce guide explique :
1.  **Authentification & Base de Données** : Créer des users, gérer des sessions sécurisées via token.
2.  **Web3** : Intégrer des wallets crypto et de la localisation live.
3.  **Boutique en Ligne** : Exposer des produits et gérer des stocks en JSON.

---

## 📋 Prérequis

*   **Docker & Docker Compose** (Recommandé)
*   Ou manuel : PHP 8.4+, Redis, MariaDB/PostgreSQL/MongoDB.

---

## 🛠 Installation & Démarrage

### 1. Via Docker (Recommandé)

```bash
# Cloner le dépôt
git clone git@github.com:bastivanconsulting/universal-api.git
cd universal-api

# Lancer l'environnement
docker-compose up -d --build
```

L'API sera accessible sur `http://localhost:8080`.

### 2. Initialisation de la Base de Données (Démo)

Pour tester les fonctionnalités de démonstration (Auth, Shop, Web3), importez le schéma fourni :

```bash
# Exemple pour MySQL/MariaDB dans le conteneur db
docker-compose exec -T db mysql -u user -ppassword database < database/schema.mysql.sql
```

---

## ⚙️ Configuration

La configuration est gérée via les variables d'environnement (`.env`).

| Variable | Description |
|----------|-------------|
| `APP_ENV` | `local` ou `production`. |
| `DB_CONNECTION` | `mysql`, `pgsql` ou `mongodb`. |
| `REDIS_HOST` | Obligatoire pour le cache/session. |

---

## 🧪 Tests des Endpoints de Démo

Une fois l'API lancée et la BDD initialisée :

*   **Inscription** : `POST /api/auth/register`
*   **Connexion** : `POST /api/auth/login` (Retourne un `token`)
*   **Mon Profil** : `GET /api/user/me` (Header `Authorization: Bearer <token>`)
*   **Crypto Balance** : `GET /api/web3/balance`
*   **Produits** : `GET /api/shop/products`

---

## 📄 Licence

Propriété de **Bastivan Consulting**. Tous droits réservés.
