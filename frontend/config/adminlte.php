<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */

    'title' => 'Alpha Print',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    */

    'logo' => '<b style="color: #fff;">Alpha</b><span style="color: #fff;">Print</span>',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'Alpha Print Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path'   => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt'    => 'Auth Logo',
            'class'  => '',
            'width'  => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    */

    'preloader' => [
        'enabled' => true,
        'mode'    => 'fullscreen',
        'img'     => [
            'path'   => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt'    => 'Alpha Print',
            'effect' => 'animation__shake',
            'width'  => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    */

    'usermenu_enabled'      => true,
    'usermenu_header'       => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image'        => false,
    'usermenu_desc'         => false,
    'usermenu_profile_url'  => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    'layout_topnav'        => null,
    'layout_boxed'         => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar'  => null,
    'layout_fixed_footer'  => null,
    'layout_dark_mode'     => null,

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    */

    'classes_body'            => '',
    'classes_brand'           => '',
    'classes_brand_text'      => '',
    'classes_content_wrapper' => '',
    'classes_content_header'  => '',
    'classes_content'         => '',
    'classes_sidebar'         => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav'     => '',
    'classes_topnav'          => 'navbar-white navbar-light',
    'classes_topnav_nav'      => 'navbar-expand',
    'classes_topnav_container'=> 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */

    'sidebar_mini'                  => 'lg',
    'sidebar_collapse'              => false,
    'sidebar_scrollbar_theme'       => 'os-theme-light',
    'sidebar_scrollbar_auto_hide'   => 'l',
    'sidebar_nav_accordion'         => true,
    'sidebar_nav_animation_speed'   => 300,

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */

    'use_route_url'          => false,
    // 👇 El logo y "dashboard_url" ahora apuntan al nuevo HOME con los 5 botones
    'dashboard_url'          => 'home',
    'logout_url'             => 'logout',
    'login_url'              => 'login',
    'register_url'           => 'register',
    'password_reset_url'     => 'password/reset',
    'password_email_url'     => 'password/email',
    'profile_url'            => false,
    'disable_darkmode_routes'=> false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path'       => 'css/app.css',
    'laravel_js_path'        => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Sidebar Menu
    |--------------------------------------------------------------------------
    */

    'menu' => [
        [
            'type' => 'sidebar-menu-search',
            'text' => 'Buscar...',
        ],

        // 🏠 Nuevo HOME (módulo con 5 botones)
        [
            'text' => 'Inicio',
            'url'  => 'home',
            'icon' => 'fas fa-home',
        ],

        // 🔔 Notificaciones (visible para todos los logueados)
        [
            'text' => 'Notificaciones',
            'url'  => 'notificaciones',
            'icon' => 'fas fa-bell',
        ],

        // 📦 Pedidos
        [
            'text' => 'Pedidos',
            'url'  => 'pedidos',
            'icon' => 'fas fa-box',
        ],

        // 📁 Archivos
        [
            'text' => 'Archivos',
            'url'  => 'archivos',
            'icon' => 'fas fa-folder-open',
        ],

        // 📅 Calendario
        [
            'text' => 'Calendario',
            'url'  => 'calendario',
            'icon' => 'fas fa-calendar-alt',
        ],

        // Historial de Estados
        [
            'text' => 'Historial de Pedidos',
            'url'  => 'historial',
            'icon' => 'fas fa-history',
        ],

        // 🧭 Separador ADMINISTRACIÓN (solo Admin)
        [
            'header' => 'ADMINISTRACIÓN',
            'can'    => 'is-admin',
        ],

        // 📊 Dashboard real (métricas) SOLO ADMIN
        [
            'text' => 'Dashboard',
            'url'  => 'dashboard', // 👈 aquí cambiamos de 'home' a 'dashboard'
            'icon' => 'fas fa-tachometer-alt',
            'can'  => 'is-admin',
        ],

        // ⭐ Valoraciones (solo Admin)
        [
            'text' => 'Valoraciones',
            'url'  => 'valoraciones',
            'icon' => 'fas fa-star',
            'can'  => 'is-admin',
        ],

        // 👤 Usuarios (solo Admin)
        [
            'text' => 'Usuarios',
            'url'  => 'usuarios',
            'icon' => 'fas fa-user',
            'can'  => 'is-admin',
        ],

        // 👷 Empleados (solo Admin)
        [
            'text' => 'Empleados',
            'url'  => 'empleados',
            'icon' => 'fas fa-user-tie',
            'can'  => 'is-admin',
        ],

        // 🧾 Clientes (solo Admin)
        [
            'text' => 'Clientes',
            'url'  => 'clientes',
            'icon' => 'fas fa-users',
            'can'  => 'is-admin',
        ],

        // 📚 Bitácora (solo Admin)
        [
            'text' => 'Bitácora',
            'url'  => 'bitacora',
            'icon' => 'fas fa-clipboard-list',
            'can'  => 'is-admin',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        // 🔐 Imprescindible para usar 'can' => 'is-admin'
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type'   => 'js',
                    'asset'  => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type'   => 'js',
                    'asset'  => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type'   => 'css',
                    'asset'  => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],

        'Chartjs' => [
            'active' => true,
            'files' => [
                [
                    'type'   => 'js',
                    'asset'  => false,
                    'location' => '//cdn.jsdelivr.net/npm/chart.js',
                ],
            ],
        ],

        'InactivityTimer' => [
            'active' => true,
            'files'  => [
                // 👇 Solo el temporizador; firebase.js ya se carga en master.blade.php
                [
                    'type'      => 'js',
                    'asset'     => true,
                    'location'  => 'js/inactivity-timer.js',
                    'attributes'=> [
                        'defer' => true,
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame Mode
    |--------------------------------------------------------------------------
    */

    'iframe' => [
        'default_tab' => [
            'url'   => null,
            'title' => null,
        ],
        'buttons' => [
            'close'            => true,
            'close_all'        => true,
            'close_all_other'  => true,
            'scroll_left'      => true,
            'scroll_right'     => true,
            'fullscreen'       => true,
        ],
        'options' => [
            'loading_screen'   => 1000,
            'auto_show_new_tab'=> true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */

    'livewire' => false,
];