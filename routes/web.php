<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\JournalisationController;
use App\Http\Controllers\TypePermissionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HistoriquePosteController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ResidenceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\StatistiqueController;

// ============================================
// ROUTES PUBLIQUES — Sous contrôle réseau local
// ============================================
Route::middleware('reseau.local')->group(function () {

    // Connexion / Inscription (Accessibles aux invités uniquement)
    Route::middleware('guest')->group(function () {

        // Formulaire de connexion
        Route::get('/', [LoginController::class, 'index'])->name('login');
        Route::get('/login', [LoginController::class, 'index']); 

        // Traitement de la connexion
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');

        // Formulaire et traitement de l'inscription
        Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
        Route::post('/register', [LoginController::class, 'register'])->name('register.post');

        // Réinitialisation du mot de passe
        Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'create'])->name('password.request');
        Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'store'])->name('password.email');
        Route::get('/reinitialiser-mot-de-passe/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reinitialiser-mot-de-passe', [ResetPasswordController::class, 'store'])->name('password.update');
    });

    // ============================================
    // ROUTES PROTÉGÉES — Utilisateurs Authentifiés
    // ============================================
    Route::middleware('auth')->group(function () {

        // Tableaux de bord & Session
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Gestion du Profil Personnel (Lecture, Mise à jour, Désactivation)
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::delete('/profil', [ProfilController::class, 'destroy'])->name('profil.destroy');

        // Gestion Autonome Sécurité / Mot de passe de l'utilisateur connecté
        Route::get('/password/edit', [PasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password/update', [PasswordController::class, 'update'])->name('password.update');

        // Action d'administration (Changement par un supérieur / Admin)
        Route::put('/admin/employee/{matricule}/reset-password', [PasswordController::class, 'resetByAdmin'])->name('admin.employee.reset-password');

        // ============================================
        // GESTION DES EMPLOYÉS
        // ============================================
        Route::prefix('employes')->name('employes.')->group(function () {
            Route::get('/',                 [EmployeController::class, 'index'])->name('index');
            Route::get('/create',           [EmployeController::class, 'create'])->name('create');
            Route::post('/',                [EmployeController::class, 'store'])->name('store');
            Route::get('/{matricule}',      [EmployeController::class, 'show'])->name('show');
            Route::get('/{matricule}/edit', [EmployeController::class, 'edit'])->name('edit');
            Route::put('/{matricule}',      [EmployeController::class, 'update'])->name('update');
            Route::delete('/{matricule}',   [EmployeController::class, 'destroy'])->name('destroy');
        });

        // ============================================
        // GESTION DES DEMANDES DE CONGÉS / PERMISSIONS
        // ============================================
        // Impression globale
        Route::get('/demandes/imprimer-registre', [DemandeController::class, 'imprimerRegistre'])->name('demandes.imprimerRegistre');
        
        // Impression individuelle d'une fiche
        Route::get('/demandes/{code_demande}/imprimer', [DemandeController::class, 'imprimerUnique'])->name('demandes.imprimerUnique');

        // CRUD complet Demandes
        Route::resource('demandes', DemandeController::class)->parameters([
            'demandes' => 'code_demande'
        ]);

        // Remplacer par :
        Route::post('/demandes/{code_demande}/valider', [DemandeController::class, 'valider'])->name('demandes.valider');

        // Circuit de validation des permissions (Visa)
        Route::get('/validations', [DemandeController::class, 'index'])->name('validations.index');
      

        // ============================================
        // PARAMÉTRAGES & PARAMÈTRES GLOBAUX (Accessibles aux connectés)
        // ============================================
        // Types de permissions
        Route::resource('type-permissions', TypePermissionController::class)->parameters([
            'type-permissions' => 'code_type_permission'
        ]);

        // Services (Impression + CRUD)
        Route::get('/services/{code_service}/imprimer', [ServiceController::class, 'imprimerFiche'])->name('services.imprimerFiche');
        Route::resource('services', ServiceController::class)->parameters([
            'services' => 'code_service'
        ]);

        // Postes (Impression + CRUD)
        Route::get('/postes/{code_poste}/imprimer', [PosteController::class, 'imprimerFiche'])->name('postes.imprimerFiche');
        Route::resource('postes', PosteController::class)->parameters([
            'postes' => 'code_poste'
        ]);

        // Contacts professionnels / d'urgence
        Route::resource('contacts', ContactController::class)->parameters([
            'contacts' => 'code_contact'
        ]);

        // Planning journalier de présence
        Route::resource('plannings', PlanningController::class)->parameters([
            'plannings' => 'code_planning'
        ]);

        // ============================================
        // GESTION DES RÉSIDENCES & ADRESSES
        // ============================================
        Route::prefix('residences')->name('residences.')->group(function () {
            Route::get('/', [ResidenceController::class, 'index'])->name('index');
            Route::get('/create', [ResidenceController::class, 'create'])->name('create');
            Route::post('/', [ResidenceController::class, 'store'])->name('store');
            Route::get('/employe/{matricule}', [ResidenceController::class, 'show'])->name('show');
            Route::get('/{code_residence}/edit', [ResidenceController::class, 'edit'])->name('edit');
            Route::put('/{code_residence}', [ResidenceController::class, 'update'])->name('update');
            Route::delete('/{code_residence}', [ResidenceController::class, 'destroy'])->name('destroy');
        });

        // ============================================
        // SÉCURITÉ AUDIT : JOURNALISATION (Espace RH / Administrateur)
        // ============================================
        Route::prefix('journalisation')->name('journalisation.')->middleware('acces.journalisation')->group(function () {
            Route::get('/', [JournalisationController::class, 'index'])->name('index');
            Route::get('/{code_log}', [JournalisationController::class, 'show'])->name('show');
        });

        // Route accessible pour les connectés qui sont inactifs
        Route::get('/compte-inactif', function () {
            return view('auth.status');
              })->middleware('auth')->name('compte.inactif');

        Route::get('/maintenance-licence', function () {
                 return view('errors.lifecycle');
                                     })->name('maintenance.licence');
       

    Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');
             
      // ===========================================
        // ACCÈS RESTREINTS : GÉRÉS PAR LE CONTROLEUR (ADMIN / RH)
        // ============================================
        Route::resource('historique_postes', HistoriquePosteController::class)->parameters([
            'historique_postes' => 'code_historique'
        ]);

    }); // Fin Middleware Auth
}); // Fin Middleware Reseau.local