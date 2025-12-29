<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal API — Bastivan Consulting</title>
    <style>
        :root {
            /* Palette raffinée */
            --bg-body: #0a0a0a;
            --bg-card: #171717;
            --border-card: #333333;
            --text-main: #ededed;
            --text-muted: #a1a1a1;
            --accent: #ffffff;
            --highlight: #3b82f6;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        /* Header minimaliste */
        header {
            margin-bottom: 6rem;
            text-align: left;
            border-bottom: 1px solid var(--border-card);
            padding-bottom: 4rem;
        }

        .brand {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--highlight);
            font-weight: 600;
            margin-bottom: 1rem;
            display: block;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0 0 1.5rem 0;
            line-height: 1.1;
            background: linear-gradient(to right, #fff, #999);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            max-width: 600px;
            font-weight: 300;
        }

        /* Grille épurée */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .card {
            background: var(--bg-card);
            padding: 2.5rem;
            border: 1px solid var(--border-card);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: var(--text-muted);
            transform: translateY(-2px);
        }

        .icon-svg {
            width: 32px;
            height: 32px;
            margin-bottom: 1.5rem;
            display: block;
            color: var(--text-muted);
            transition: color 0.3s, transform 0.3s;
        }

        .card:hover .icon-svg {
            color: var(--highlight);
            transform: scale(1.1);
        }

        .card h2 {
            font-size: 1.2rem;
            margin: 0 0 1rem 0;
            color: var(--text-main);
            font-weight: 600;
        }

        .card p {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.7;
        }

        /* Footer discret */
        footer {
            margin-top: 6rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-card);
            display: flex;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        a.cta {
            display: inline-block;
            margin-top: 2rem;
            color: var(--text-main);
            text-decoration: none;
            border-bottom: 1px solid var(--highlight);
            padding-bottom: 2px;
            transition: opacity 0.2s;
        }

        a.cta:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            h1 { font-size: 2.5rem; }
            .container { padding: 2rem 1.5rem; }
            footer { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <span class="brand">Bastivan Consulting</span>
            <h1>L'excellence technique<br>au service de vos API.</h1>
            <p class="subtitle">Une architecture Universal API robuste, sécurisée et modulaire pour les environnements cloud exigeants.</p>
            <a href="/status" class="cta">Consulter l'état des services &rarr;</a>
        </header>

        <div class="grid">
            <!-- Présentation -->
            <div class="card">
                <!-- Rocket Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"></path><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"></path><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"></path></svg>
                <h2>Performance & Design</h2>
                <p>
                    Une fondation MVC légère conçue pour la vitesse. Une structure de code claire qui privilégie la lisibilité et la maintenabilité sans sacrifier la puissance.
                </p>
            </div>
            
            <!-- Sécurité -->
            <div class="card">
                <!-- Lock Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <h2>Sécurité Maximale</h2>
                <p>
                    SSL/TLS strict, protections anti-injections SQL via PDO et en-têtes de sécurité HTTP renforcés. La protection de vos données est native, pas optionnelle.
                </p>
            </div>

            <!-- Docker & K8s -->
            <div class="card">
                <!-- Server/Container Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <h2>Cloud Native</h2>
                <p>
                    Déploiement instantané avec Docker et Kubernetes. Architecture stateless prête pour la scalabilité horizontale et la haute disponibilité.
                </p>
            </div>

            <!-- S3 -->
            <div class="card">
                <!-- Cloud Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19c0-3.037-2.463-5.5-5.5-5.5S6.5 15.963 6.5 19"></path><path d="M17.5 19H19a5 5 0 0 0 4-5 5 5 0 0 0-5-5h-1a6 6 0 0 0-10.9-1 4 4 0 0 0-6.1 4 4 4 0 0 0 4 6h1.5"></path></svg>
                <h2>Stockage Distribué (S3)</h2>
                <p>
                    Intégration transparente avec S3 pour le stockage de configurations et d'assets. Conçu pour garantir la persistance des données en environnement clusterisé.
                </p>
            </div>

            <!-- Redis -->
            <div class="card">
                <!-- Lightning Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <h2>Redis & Rate Limiting</h2>
                <p>
                    Gestion avancée du trafic et protection contre les abus grâce au Rate Limiting adossé à Redis. Vos ressources restent disponibles et performantes.
                </p>
            </div>

            <!-- Hooks -->
            <div class="card">
                <!-- Puzzle Icon -->
                <svg class="icon-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="3" width="7" height="7"></rect></svg>
                <h2>Architecture de Hooks</h2>
                <p>
                    Étendez les fonctionnalités du cœur sans le modifier. Le système de hooks permet une personnalisation profonde tout en facilitant les mises à jour futures.
                </p>
            </div>
        </div>

        <footer>
            <span>&copy; <?php echo date('Y'); ?> Bastivan Consulting.</span>
            <span>Universal API v1.0</span>
        </footer>
    </div>
</body>
</html>
