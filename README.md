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

---

**Développé avec ❤️ par Bastivan Consulting**
