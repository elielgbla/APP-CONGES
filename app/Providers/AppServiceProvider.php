<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pagination Bootstrap pour tous les liens paginate()/links()
        Paginator::useBootstrap();

        // Configurations pour le développement local sous Windows
        if (app()->environment('local')) {

            if (!app()->runningInConsole()) {
                // 1. Utiliser la mémoire vive (RAM) pour les données temporaires
                config(['cache.default' => 'array']);

                // 2. IMPORTANT : On laisse le chemin physique de 'view.compiled' intact pour Laravel,
                // mais on demande au moteur d'ignorer la vérification agressive des dates de fichiers (.tmp)
                config(['view.expires' => false]);
            }

            // 3. Application automatique des permissions de secours sur le dossier physiques des vues
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $viewPath = storage_path('framework/views');

                // Si le dossier existe, on s'assure qu'il est accessible à 100%
                if (is_dir($viewPath)) {
                    @chmod($viewPath, 0777);
                    // Commande Windows en tâche de fond pour écraser les verrous hérités
                    @shell_exec('icacls "' . $viewPath . '" /grant *S-1-1-0:(OI)(CI)F /T > NUL 2>&1');
                }
            }
        }
    }
}
