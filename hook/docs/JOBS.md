# Système de Jobs & Files d'Attente (Queue)

L'Universal API intègre un système de file d'attente performant basé sur **Redis**.
Il permet de décharger les tâches lourdes (envoi d'emails, traitement d'images, calculs blockchain) pour ne pas ralentir la réponse HTTP.

## 1. Créer un Job

Un Job est une classe PHP simple qui étend `Bastivan\UniversalApi\Core\Queue\Job` et implémente la méthode `handle()`.
Placez vos jobs dans le dossier `hook/Jobs/`.

**Exemple : `hook/Jobs/SendWelcomeEmail.php`**

```php
<?php

namespace Bastivan\UniversalApi\Hook\Jobs;

use Bastivan\UniversalApi\Core\Queue\Job;
use Bastivan\UniversalApi\Core\Logger;

class SendWelcomeEmail extends Job
{
    private string $email;
    private string $username;

    public function __construct(string $email, string $username)
    {
        $this->email = $email;
        $this->username = $username;
    }

    /**
     * Cette méthode sera exécutée par le Worker en arrière-plan.
     */
    public function handle(): void
    {
        // Logique d'envoi d'email (exemple simulé)
        Logger::info("Envoi de l'email de bienvenue à {$this->username} ({$this->email})");

        // mail($this->email, "Bienvenue !", "...");

        // Simuler un travail long
        sleep(2);
    }
}
```

---

## 2. Dispatcher un Job (Mise en file)

Dans vos Contrôleurs, au lieu d'exécuter la tâche directement, vous "pushez" le job dans la queue. C'est instantané pour l'utilisateur.

**Exemple dans un Contrôleur :**

```php
use Bastivan\UniversalApi\Core\Queue\Queue;
use Bastivan\UniversalApi\Hook\Jobs\SendWelcomeEmail;

public function register()
{
    // ... Création de l'utilisateur en BDD ...

    // Déclencher le job asynchrone
    Queue::push(new SendWelcomeEmail('user@example.com', 'JeanDupont'));

    echo json_encode(['message' => 'Inscription réussie, email en cours d\'envoi.']);
}
```

---

## 3. Le Worker

Les jobs ne s'exécutent pas tout seuls. Un processus "Worker" doit tourner en arrière-plan pour dépiler et exécuter les tâches.

L'image Docker inclut déjà le worker (géré via `supervisord` ou lancé manuellement).

Pour lancer le worker manuellement (en développement) :
```bash
php bin/worker.php
```

Le worker surveille la clé Redis `queue:default` et exécute la méthode `handle()` de chaque Job désérialisé.

---

## 4. Bonnes Pratiques

*   **Constructeur léger** : Ne faites pas de calculs lourds dans le `__construct`. Passez juste les données nécessaires (ID, Email, etc.). Le constructeur est exécuté lors de la requête HTTP (avant sérialisation).
*   **Idempotence** : Assurez-vous que vos jobs peuvent être relancés sans casser les données (au cas où le job échoue et est redémarré).
*   **Logs** : Utilisez `Logger::info()` ou `Logger::error()` dans vos jobs pour suivre leur exécution.
