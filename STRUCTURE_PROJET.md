# Structure du projet APP_CONGE

Ce fichier présente l'arborescence corrigée et les fichiers principaux qui sont les plus importants à manipuler.

## Arborescence principale

- artisan
- composer.json
- package.json
- phpunit.xml
- README.md
- vite.config.js
- app/
  - Http/
    - Controllers/
      - Auth/
        - LoginController.php
        - ForgotPasswordController.php
        - ResetPasswordController.php
        - PasswordController.php
      - DashboardController.php
      - EmployeController.php
      - DemandeController.php
      - ValidationController.php
      - ProfilController.php
      - JournalisationController.php
      - ServiceController.php
      - PosteController.php
      - TypePermissionController.php
      - ContactController.php
      - HistoriquePosteController.php
      - PlanningController.php
      - ResidenceController.php
      - StatistiqueController.php
    - Middleware/
      - CheckLocalNetwork.php
      - CheckCompteActif.php
      - CheckAppLifecycle.php
      - LogActivity.php
    - Requests/
      - RegisterRequest.php
      - StoreDemandeRequest.php
      - StoreEmployeRequest.php
      - UpdateProfilRequest.php
  - Models/
    - Employe.php
    - Demande.php
    - Validation.php
    - HistoriquePoste.php
    - Journalisation.php
    - Planning.php
    - Service.php
    - Poste.php
    - TypePermission.php
    - Contact.php
    - Residence.php
    - Localite.php
    - Genre.php
    - Categorie.php
    - TypeContact.php
    - User.php
  - Providers/
    - AppServiceProvider.php
    - WorkflowServiceProvider.php
  - Services/
    - WorkflowPermissionService.php
- bootstrap/
  - app.php
  - providers.php
  - cache/
    - packages.php
    - services.php
- config/
  - app.php
  - auth.php
  - cache.php
  - database.php
  - filesystems.php
  - logging.php
  - mail.php
  - queue.php
  - services.php
  - session.php
  - view.php
- database/
  - migrations/
  - seeders/
  - factories/
  - app_conges.sql
- public/
  - index.php
  - robots.txt
  - build/
  - css/
  - js/
  - images/
  - storage/
  - webfonts/
- resources/
  - views/
  - css/
  - js/
- routes/
  - web.php
  - console.php
- storage/
  - app/
  - framework/
  - logs/
- tests/
  - Feature/
  - Unit/
- vendor/ (dépendances)
- node_modules/ (dépendances front-end)

## Fichiers principaux à manipuler

### Configuration et installation
- `composer.json` : dépendances PHP du projet.
- `package.json` : dépendances JavaScript et scripts NPM.
- `routes/web.php` : routes principales de l'application.
- `README.md` : documentation générale du projet.

### Authentification et sessions
- `app/Http/Controllers/Auth/LoginController.php` : connexion, inscription, journalisation des sessions.
- `app/Http/Controllers/Auth/ForgotPasswordController.php` : demande de réinitialisation.
- `app/Http/Controllers/Auth/ResetPasswordController.php` : réinitialisation de mot de passe.
- `app/Http/Controllers/Auth/PasswordController.php` : modification de mot de passe.
- `app/Http/Requests/RegisterRequest.php` : règles de validation à l'inscription.

### Gestion des demandes de congés et permissions
- `app/Http/Controllers/DemandeController.php` : création, édition, suppression, affichage et validation de demandes.
- `app/Services/WorkflowPermissionService.php` : règles de visibilité et validation des demandes selon le rôle.
- `app/Models/Demande.php` : modèle de la demande.
- `app/Http/Requests/StoreDemandeRequest.php` : validation des demandes.

### Circuit de validation
- `app/Http/Controllers/ValidationController.php` : enregistrement des visas N1 à N4.
- `app/Models/Validation.php` : modèle de la table de validation.
- `app/Models/HistoriquePoste.php` : historisation des postes et services des employés.
- `app/Models/Employe.php` : récupération du poste/service actif et rôle.

### Gestion des employés et annexes
- `app/Http/Controllers/EmployeController.php` : CRUD des employés et mise à jour de profil.
- `app/Http/Controllers/ProfilController.php` : gestion du profil connecté.
- `app/Models/Employe.php` : modèle principal utilisateur.
- `app/Http/Controllers/ServiceController.php`, `PosteController.php`, `TypePermissionController.php` : paramétrage métier.

### Sécurité et règles métier
- `app/Http/Middleware/CheckLocalNetwork.php` : accès réservé au réseau local.
- `app/Http/Middleware/CheckCompteActif.php` : blocage des comptes inactifs.
- `app/Http/Middleware/CheckAppLifecycle.php` : contrôle de la durée de vie / maintenance.
- `app/Http/Middleware/LogActivity.php` : journalisation des actions de navigation.

### Audit et logs
- `app/Http/Controllers/JournalisationController.php` : consultation des traces d'audit.
- `app/Models/Journalisation.php` : modèle des logs.

### Autres modules utiles
- `app/Http/Controllers/PlanningController.php` : création de plannings d'absence.
- `app/Http/Controllers/ResidenceController.php` : gestion des résidences et adresses.
- `app/Http/Controllers/ContactController.php` : contacts professionnels / urgents.
- `app/Http/Controllers/StatistiqueController.php` : statistiques.

## Conseils de manipulation

- Modifier principalement `routes/web.php`, les contrôleurs du dossier `app/Http/Controllers/`, et les services dans `app/Services/`.
- Ne pas modifier `vendor/` ni `node_modules/` sauf en cas de mise à jour de dépendances via `composer update` ou `npm update`.
- Les vues sont dans `resources/views/`; modifier ces fichiers si tu changes l'interface.
- Les paramètres applicatifs sont dans `config/`; change uniquement si tu veux modifier le comportement global ou la configuration du framework.

## Remarques

- La structure réelle du projet correspond bien à une application Laravel personnalisée.
- Le dossier `node_modules` contient les dépendances front-end et n'est pas à manipuler directement sauf si tu travailles sur l'environnement de build.
- Le dossier `vendor` contient les dépendances PHP et doit rester géré par Composer.


langage utilise php/laravel
bootstrap et css
