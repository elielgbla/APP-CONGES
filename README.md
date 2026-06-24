# APP_CONGE

## Présentation générale

`APP_CONGE` est une application Laravel 13.8 conçue pour gérer les demandes de congés et de permissions au sein d'une organisation. Elle implémente un circuit de validation à plusieurs niveaux (N1 à N4), un suivi du personnel, un contrôle d'accès par rôle, des journaux d'audit et des données de planification.

Le projet est également protégé par une règle réseau local (`reseau.local`) : l'application est accessible uniquement depuis l'environnement interne de l'entreprise.

---

## Structure du projet

### Arborescence principale

- `artisan` : CLI Laravel.
- `composer.json` : dépendances PHP et scripts de projet.
- `package.json` : dépendances front-end et scripts NPM.
- `phpunit.xml` : configuration des tests.
- `vite.config.js` : configuration Vite pour les assets frontend.
- `app/` : logique métier de l'application.
- `bootstrap/` : chargement de l'application Laravel.
- `config/` : configuration des services et du framework.
- `database/` : migrations, seeders et dump SQL.
- `public/` : point d'entrée web.
- `resources/` : vues, JS et CSS source.
- `routes/` : définition des routes HTTP.
- `storage/` : logs, cache et fichiers générés.
- `tests/` : tests unitaires et fonctionnels.
- `vendor/` : dépendances externes.

### Dossiers clés

#### `app/Http/Controllers`

Contient les contrôleurs métiers principaux :

- `Auth/` : connexion, inscription, mot de passe oublié/reinitialisation.
- `DashboardController` : affichage des stats et du tableau de bord utilisateur.
- `EmployeController` : gestion des collaborateurs (CRUD, profils, fiches).
- `DemandeController` : création, modification, consultation et validation des demandes.
- `ValidationController` : enregistrement du visa de validation pour chaque niveau.
- `JournalisationController` : consultation des logs d'audit.
- `ServiceController`, `PosteController`, `TypePermissionController`, `PlanningController`, `ResidenceController`, `ContactController`, etc.

#### `app/Models`

Modèles Eloquent qui reflètent les tables métiers :

- `Employe` : utilisateur authentifiable, responsable du profil, des accès et relations.
- `Demande` : demande de congé / permission.
- `Validation` : décision de validation par niveau.
- `HistoriquePoste` : piste d'affectation poste/service de l'employé.
- `Journalisation` : logs d'audit des actions.
- `Planning` : créneaux d'absence générés après validation finale.
- `Service`, `Poste`, `TypePermission`, `Contact`, `Residence`, `Localite`, `Genre`, `Categorie`.

#### `app/Http/Middleware`

Sécurise le comportement applicatif :

- `CheckLocalNetwork` : accès uniquement depuis le réseau local.
- `CheckCompteActif` : redirige les comptes inactifs vers une page de statut.
- `CheckAppLifecycle` : vérifie la date de vie de l'application / maintenance licence.
- `LogActivity` : journalisation des actions de navigation ou d'authentification.

#### `app/Http/Requests`

Contient les règles de validation dédiées :

- `RegisterRequest` : validation de l'inscription.
- `StoreDemandeRequest` : validation de la création d'une demande.
- `StoreEmployeRequest` : validation de la création d'un employé.
- `UpdateProfilRequest` : validation de la mise à jour de profil.

#### `app/Services`

Services métiers :

- `WorkflowPermissionService` : logique de filtrage des demandes visibles selon le rôle, et enregistrement des validations métier.

---

## Fonctionnement détaillé

### Authentification et accès

- Les routes publiques sont protégées par le middleware `reseau.local` et accessibles seulement en réseau interne.
- Les pages de connexion, d'inscription et de mot de passe oublié sont accessibles aux invités (`guest`).
- Après authentification, les utilisateurs accèdent aux routes protégées par `auth`.
- Les comptes marqués `inactif` ou `suspendu` sont redirigés vers `/compte-inactif`.
- La licence/mode maintenance est contrôlée par `CheckAppLifecycle` et redirige vers `/maintenance-licence` si nécessaire.

### Rôles et niveaux de validation

Le système repose sur un code de poste actif enregistré via `historique_poste` :

- `EMPLOYE` : collaborateur ordinaire.
- `RESP_SERVI` / `RESPONSABLE` : responsable de service, validateur N1.
- `RH` : ressources humaines, validateur N2.
- `DIR_EXPLOI` / `DIR_EXPLOITATION` : directeur d'exploitation, validateur N3.
- `DIR_GENERA` / `DIR_GENERAL` : directeur général, validateur N4.
- `ADMIN` : rôle administratif avec visibilité totale, mais sans validation active dans `WorkflowPermissionService`.

### Circuit de demande

1. `EMPLOYE` crée une demande via `DemandeController::store()`.
2. La demande naît avec le statut `en_attente_n1`.
3. Premier visa : `RESP_SERVI` autorisé si l'employé appartient au même service actif.
4. Deuxième visa : `RH` valide le dossier et le passe en `en_attente_n3`.
5. Troisième visa : `DIR_EXPLOI`/`DIR_EXPLOITATION` passe en `en_attente_n4`.
6. Quatrième visa : `DIR_GENERA`/`DIR_GENERAL` approuve définitivement la demande.
7. Si la demande est rejetée à tout niveau, son statut devient `rejetee` et le circuit s'arrête.

### Statuts de demande

- `en_attente_n1` : en attente du niveau 1.
- `en_attente_n2` : en attente du niveau 2.
- `en_attente_n3` : en attente du niveau 3.
- `en_attente_n4` : en attente du niveau 4.
- `approuvee` : dossier finalisé et approuvé.
- `rejetee` : dossier rejeté.

### Validation et enregistrement métier

- `ValidationController::store()` enregistre chaque décision dans la table `validation_permission`.
- Si l'action est `valide`, le statut évolue vers le niveau suivant.
- Si l'action est `rejete`, la demande passe immédiatement à `rejetee`.
- L'approbation finale (`N4`) déclenche aussi la création d'un `Planning` pour bloquer la période d'absence de l'employé.

### Flux de visualisation des demandes

- `DemandeController::index()` utilise `WorkflowPermissionService::getDemandesVisiblesPourUser()` pour filtrer :
  - les employés ne voient que leurs propres demandes.
  - les responsables de service voient les demandes N1 de leur service.
  - les RH voient les demandes N2.
  - le directeur exploitation voit N3.
  - le directeur général voit N4.
  - l'admin voit toutes les demandes.
- La recherche globale peut filtrer par `code_demande`, `motif`, `statut_demande`, nom/prénom/matricule de l'employé.

### Gestion des employés

- `EmployeController` propose les opérations CRUD réservées aux rôles `RH` et `ADMIN`.
- Un employé dispose d'un `historiquePoste` qui définit son `code_poste` et `code_service` actif.
- La relation `affectationActuelle()` permet de récupérer l'affectation courante par rapport à `date_fin`.
- Les employés ont plusieurs relations : demandes, contacts, résidences, plannings, journalisations.

### Journalisation et audit

- Toutes les actions importantes (connexion, inscription, déconnexion, création/modification) sont tracées dans `Journalisation`.
- `JournalisationController` permet aux profils `ADMIN` et `RH` de consulter ces logs.
- Les routes d'audit sont également protégées par un middleware dédié via `HasMiddleware`.

### Modules de paramétrage

Le projet contient plusieurs entités de configuration métier :

- `TypePermission` : types de congés/permissions.
- `Service` : services de l'entreprise.
- `Poste` : postes et niveaux de validation.
- `Contact` : contacts professionnels ou d'urgence.
- `Planning` : calendrier d'absence créé après validation.
- `Residence` : adresses et localités des employés.

### Règles de données importantes

- Les tables utilisent des noms singuliers (`employe`, `demande`, `service`, `poste`, etc.).
- Les clés primaires sont souvent des codes textuels (`code_demande`, `code_service`, `matricule_employe`).
- Certaines tables désactivent les timestamps Eloquent (`public $timestamps = false`).
- Les dates de fin d'affectation (`date_fin`) déterminent les services et postes actifs.

---

## Installation locale

1. Copier `.env.example` en `.env`.
2. Configurer la base de données MySQL / MariaDB.
3. Exécuter :
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate --force`
4. Si nécessaire : `php artisan db:seed` pour remplir les tables de référence.
5. Lancer le serveur local : `php artisan serve`.

> Note : ce projet est prévu pour être utilisé en réseau local et contrôle l'accès via l'adresse IP du client.

---

## Commandes utiles

- `composer install` : installe les dépendances PHP.
- `npm install` : installe les dépendances front-end.
- `npm run dev` : lance l'environnement de développement Vite.
- `php artisan migrate` : applique les migrations.
- `php artisan test` : exécute les tests.

---

## Règles métier à connaître

- Un employé ne peut créer une demande que pour lui-même, sauf s'il a un rôle RH ou responsable de service.
- Les responsables de service ne peuvent valider que les demandes de leur service.
- Les niveaux de validation sont stricts et ne peuvent pas être sautés.
- L'`ADMIN` a une visibilité élargie mais ne doit pas valider les demandes dans le moteur `WorkflowPermissionService`.
- Les comptes `inactif`/`suspendu` ne peuvent pas naviguer dans l'application normale.

---

## Points de vigilance

- La sécurité réseau est active via `CheckLocalNetwork`.
- La licence d'utilisation est contrôlée par `CheckAppLifecycle`.
- Les logs d'audit sont immuables : le contrôleur `JournalisationController` bloque toute création, mise à jour ou suppression manuelle.
- Les données sensibles (mot de passe) sont hachées avec `Hash::make()`.

---

## Fichiers importants à consulter ensuite

- `routes/web.php` : définition complète des routes publiques et protégées.
- `app/Services/WorkflowPermissionService.php` : règles de filtrage et validation.
- `app/Models/Employe.php` : relations et gestion du poste/service actif.
- `app/Http/Controllers/DemandeController.php` : création / modification / validation des demandes.
- `app/Http/Controllers/ValidationController.php` : circuit de validation N1 → N4.
- `app/Http/Middleware/CheckLocalNetwork.php` : politique réseau local.
- `app/Http/Requests/StoreDemandeRequest.php` : règles de validation métier pour les demandes.

---

## Résumé rapide

`APP_CONGE` est une application Laravel métier pour la gestion des congés/permissions, avec :

- un circuit de validation hiérarchique à 4 niveaux,
- une gestion du personnel et des services,
- une journalisation d'audit,
- une sécurité réseau locale,
- des entités métiers clairement séparées dans `app/Models` et `app/Http/Controllers`.



