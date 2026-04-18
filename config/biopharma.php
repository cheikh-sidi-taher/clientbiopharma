<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inscription publique
    |--------------------------------------------------------------------------
    |
    | Si false, les routes /register renvoient 404. Les comptes sont créés
    | par un administrateur (Utilisateurs) ou via les seeders.
    |
    */

    'allow_registration' => (bool) env('ALLOW_REGISTRATION', false),

];
