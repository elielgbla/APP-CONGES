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
    // Dans config/view.php
'compiled' => env(
    'VIEW_COMPILED_PATH',
    sys_get_temp_dir() // Utilise le dossier TEMP de Windows à la place de storage
),

];