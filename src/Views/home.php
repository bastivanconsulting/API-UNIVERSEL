<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Universal API - Bastivan Consulting</title>
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --accent-color: #38bdf8;
            --accent-gradient: linear-gradient(135deg, #38bdf8, #818cf8);
            --card-border: #334155;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero Section */
        header {
            text-align: center;
            padding: 4rem 0;
            animation: fadeIn 1s ease-in;
        }

        h1 {
            font-size: 4rem;
            margin: 0;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .subtitle {
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-top: 1rem;
            font-weight: 300;
        }

        .cta-button {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.8rem 2rem;
            background: var(--accent-gradient);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3);
        }

        /* Grid Layout */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: var(--bg-secondary);
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid var(--card-border);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
        }

        .card h2 {
            font-size: 1.5rem;
            margin-top: 0;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card p {
            color: var(--text-secondary);
        }

        /* Footer */
        footer {
            text-align: center;
            margin-top: 4rem;
            padding: 2rem 0;
            border-top: 1px solid var(--card-border);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            h1 { font-size: 2.5rem; }
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Universal API</h1>
            <p class="subtitle">Une solution robuste et moderne par <strong>Bastivan Consulting</strong></p>
            <a href="/status" class="cta-button">Vérifier le statut de l'API</a>
        </header>

        <div class="grid">
            <!-- Présentation -->
            <div class="card">
                <h2>🚀 Présentation</h2>
                <p>
                    Universal API est une fondation légère et modulaire conçue pour construire des services web nouvelle génération.
                    Elle offre une structure claire (MVC) tout en restant extrêmement flexible pour répondre aux besoins métiers spécifiques.
                </p>
            </div>

            <!-- Efficacité -->
            <div class="card">
                <h2>⚡ Efficacité</h2>
                <p>
                    Construite sur <strong>PHP 8.4+</strong>, cette API tire parti de la compilation JIT et des dernières optimisations du langage.
                    L'architecture minimise l'empreinte mémoire et maximise le débit des requêtes.
                </p>
            </div>

            <!-- Sécurité -->
            <div class="card">
                <h2>🔒 Sécurité</h2>
                <p>
                    La sécurité n'est pas une option. SSL/TLS obligatoire, en-têtes de sécurité stricts, et utilisation systématique de PDO
                    pour prévenir les injections SQL. Vos données sont protégées par défaut.
                </p>
            </div>

            <!-- Cloud Native -->
            <div class="card">
                <h2>🐳 Docker & K8s</h2>
                <p>
                    Conçue pour le Cloud. Des images Docker optimisées et des manifestes Kubernetes prêts à l'emploi garantissent
                    un déploiement fluide, une haute disponibilité et une scalabilité horizontale immédiate.
                </p>
            </div>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Bastivan Consulting. Tous droits réservés.</p>
            <p>Propulsé par Universal API</p>
        </footer>
    </div>
</body>
</html>
