<?php

return [
    /*
     *  Automatic registration of routes will only happen if this setting is `true`
     */
    'enabled' => true,

    /*
     * Controllers in these directories that have routing attributes
     * will automatically be registered.
     */
    'directories' => [
        app_path('Modules/Account/Controllers'),
        app_path('Modules/Auth/Controllers'),
        app_path('Modules/Pix/Controllers'),
    ],

    /**
     * This middleware will be applied to all routes.
     */
    'middleware' => [
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\ForceJson::class
    ]
];
