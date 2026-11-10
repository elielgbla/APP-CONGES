<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Détermination de l'expiration des vues
    |--------------------------------------------------------------------------
    | En mettant true, Laravel vérifie la date du fichier avant de le recompiler.
    | Évite les conflits de renommage de fichiers temporaires (.tmp) sous Windows.
    */
    'expires' => true,

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views')) ?: storage_path('framework/views'),
    ),

];