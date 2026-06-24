<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Application globale au groupe 'web' pour intercepter automatiquement les comptes inactifs
        $middleware->web(append: [
            \App\Http\Middleware\CheckCompteActif::class,
            \App\Http\Middleware\CheckAppLifecycle::class, 
        ]);

        // Enregistrement des alias de tes middlewares personnalisés
        $middleware->alias([
            'reseau.local'          => \App\Http\Middleware\CheckLocalNetwork::class,
            'log.activity'          => \App\Http\Middleware\LogActivity::class,
            'acces.journalisation'  => \App\Http\Middleware\LogActivity::class, 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();