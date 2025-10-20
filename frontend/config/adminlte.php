<?php

return [

    'title' => 'AdminLTE 3',
    'title_prefix' => '',
    'title_postfix' => '',

    'use_ico_only' => false,
    'use_full_favicon' => false,

    'google_fonts' => ['allowed' => true],

    'logo' => '<b>Admin</b>LTE',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_alt' => 'Admin Logo',

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    'usermenu_enabled' => true,

    'classes_sidebar' => 'sidebar-dark-primary elevation-4',

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',

    'menu' => [
        // Navbar items
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items
        ['header' => 'PRINCIPAL'],
        [
            'text' => 'Dashboard',
            'url'  => 'home',
            'icon' => 'fas fa-tachometer-alt',
        ],
        [
            'text' => 'Pedidos',
            'url'  => 'pedidos',
            'icon' => 'fas fa-box',
        ],
        [
            'text' => 'Valoraciones',
            'url'  => 'valoraciones',
            'icon' => 'fas fa-star',
        ],

        ['header' => 'ADMINISTRACIÓN'],
        [
            'text' => 'Usuarios',
            'url'  => 'usuarios',
            'icon' => 'fas fa-user',
        ],
        [
            'text' => 'Empleados',
            'url'  => 'empleados',
            'icon' => 'fas fa-user-tie',
        ],
        [
            'text' => 'Clientes',
            'url'  => 'clientes',
            'icon' => 'fas fa-users',
        ],
        [
            'text' => 'Bitácora',
            'url'  => 'bitacora',
            'icon' => 'fas fa-history',
        ],
        [
            'text' => 'Notificaciones',
            'url'  => 'notificaciones',
            'icon' => 'fas fa-bell',
        ],
    ],

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    'livewire' => false,
];
