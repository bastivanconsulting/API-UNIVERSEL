# Universal API by Bastivan Consulting

Bienvenue sur la fondation de l'API Universelle développée par **Bastivan Consulting**.

Ce projet fournit une architecture robuste, sécurisée et extensible pour des APIs modernes en **PHP 8.4+**. Elle est conçue pour la haute disponibilité, la performance, et la sécurité dès la conception.

---

## 🚀 Fonctionnalités Clés

### 🏗 Architecture & Core
*   **Architecture MVC Légère** : Une structure claire sans la lourdeur des frameworks monolithiques.
*   **Multi-Base de Données** : Support natif et optimisé pour **MySQL/MariaDB**, **PostgreSQL**, **MongoDB** et **Redis**.
*   **Haute Disponibilité (HA)** :
    *   Chargement de la configuration dynamique (Support S3).
    *   Gestion centralisée des logs (Local ou S3).
    *   Endpoints de santé (`/status`) pour Kubernetes/Load Balancers.

### ⚡ Performance
*   **Moteur de Routing Hybride** : Optimisation O(1) pour les routes statiques via HashMap et support Regex pour les routes dynamiques.
*   **Caching Avancé** : Cache complet des réponses (Body, Headers, Status) dans Redis.
*   **Rate Limiting** : Protection contre les abus via Redis (Atomic Increments).
*   **Compression** : Support GZIP natif.

### 🔒 Sécurité (Security by Design)
*   **Protection CSRF** : Validation stricte pour les requêtes modifiant l'état (POST, PUT, etc.).
*   **Headers de Sécurité** : HSTS, CSP, X-Content-Type-Options, X-Frame-Options forcés par défaut.
*   **IP Whitelisting & Trusted Proxies** : Résolution intelligente des IPs réelles (Cloudflare, Gcore) et filtrage par IP/CIDR.
*   **Prepared Statements** : Toutes les interactions SQL utilisent `PDO` préparé pour bloquer les injections SQL.
*   **Contrôle des Méthodes** : Restriction granulaire des méthodes HTTP autorisées (Globalement ou par Route).

---

## 📋 Prérequis

*   **Docker & Docker Compose** (Recommandé)
*   Ou manuel :
    *   PHP 8.4+ (Extensions: `pdo`, `pdo_mysql`, `pdo_pgsql`, `mongodb`, `redis`, `ctype`, `curl`)
    *   Redis Server 6+
    *   MariaDB 10.6+ / PostgreSQL 14+ / MongoDB 5+

---

## 🛠 Installation & Démarrage

### 1. Via Docker (Recommandé)

C'est la méthode la plus simple pour démarrer un environnement complet.

```bash
# Cloner le dépôt
git clone git@github.com:bastivanconsulting/universal-api.git
cd universal-api

# Lancer l'environnement
docker-compose up -d --build
```

L'API sera accessible sur `http://localhost:8080`.

### 2. Installation Manuelle

```bash
# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
# (Éditez .env selon vos besoins)

# Lancer le serveur de développement
php -S localhost:8000 -t public
```

---

## ⚙️ Configuration

La configuration est gérée via les variables d'environnement (`.env`). Voici les principales options :

### Général
| Variable | Description |
|----------|-------------|
| `APP_ENV` | `local` ou `production`. En production, les erreurs sont masquées. |
| `APP_DEBUG` | `true` pour activer les traces détaillées. |
| `APP_PORT` | Port d'écoute (Défaut: 8080). |

### Base de Données & Redis
| Variable | Description |
|----------|-------------|
| `DB_CONNECTION` | `mysql`, `pgsql` ou `mongodb`. |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE` | Paramètres de connexion BDD. |
| `REDIS_HOST`, `REDIS_PORT` | Paramètres Redis (Obligatoire pour le RateLimit/Cache). |

### Sécurité
| Variable | Description |
|----------|-------------|
| `SECURITY_ALLOWED_METHODS` | Liste des méthodes autorisées globalement (ex: `GET,POST`). |
| `SECURITY_ACCESS_STRATEGY` | `ALLOW_ALL` ou `WHITELIST` (Filtrage IP global). |
| `SECURITY_ALLOWED_IPS` | Liste des IPs autorisées si la stratégie est `WHITELIST`. |
| `CORS_ALLOWED_ORIGINS` | Domaines autorisés pour le CORS (ex: `https://mon-app.com`). |

---

## 🪝 Extensibilité (Hooks)

L'API utilise un système de **Hooks** puissant situé dans le dossier `hook/`.
Cela permet d'ajouter des routes, des contrôleurs, des vues et des middlewares **sans modifier le cœur du framework** dans `src/`.

👉 **Voir la documentation complète des Hooks : [hook/README.md](hook/README.md)**

---

## 🧪 Tests & Vérification

Pour vérifier que l'API fonctionne correctement :

1.  Accédez à la page d'accueil : `http://localhost:8080/` (Doit afficher la page "Universal API").
2.  Vérifiez le statut du système : `http://localhost:8080/status` (Retourne JSON avec l'état DB/Redis).

---

## 📄 Licence

Propriété de **Bastivan Consulting**. Tous droits réservés.
