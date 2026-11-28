<?php

// Single source of truth for the admin panel side menu and its permissions.
// Add an item here and it automatically appears in:
//   - the sidebar (for roles granted its permission)
//   - the Roles access editor (/admin/roles)
//   - route enforcement (routes/admin.php uses role_or_permission with these names)

return [
    // Roles used by the storefront (auto-assigned on registration) — never
    // shown in the Roles & Access editor and never editable there.
    'storefront_roles' => ['customer', 'vendor'],

    'items' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'group' => 'Main',
            'route' => 'admin.dashboard',
            'permission' => null,
            'actions' => [],
        ],
        [
            'key' => 'products',
            'label' => 'Products',
            'group' => 'Catalog',
            'route' => 'admin.products.index',
            'permission' => 'products.view',
            'actions' => [
                'products.create' => 'Create',
                'products.edit' => 'Edit',
                'products.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'categories',
            'label' => 'Categories',
            'group' => 'Catalog',
            'route' => 'admin.categories.index',
            'permission' => 'categories.view',
            'actions' => [
                'categories.create' => 'Create',
                'categories.edit' => 'Edit',
                'categories.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'brands',
            'label' => 'Brands',
            'group' => 'Catalog',
            'route' => 'admin.brands.index',
            'permission' => 'brands.view',
            'actions' => [
                'brands.create' => 'Create',
                'brands.edit' => 'Edit',
                'brands.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'media',
            'label' => 'Media',
            'group' => 'Catalog',
            'route' => 'admin.media.index',
            'permission' => 'media.view',
            'actions' => [
                'media.create' => 'Upload',
                'media.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'attributes',
            'label' => 'Attributes',
            'group' => 'Catalog',
            'route' => 'admin.attributes.index',
            'permission' => 'attributes.view',
            'actions' => [
                'attributes.create' => 'Create',
                'attributes.edit' => 'Edit',
                'attributes.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'customers',
            'label' => 'Customers',
            'group' => 'Catalog',
            'route' => 'admin.customers.index',
            'permission' => 'customers.view',
            'actions' => [
                'customers.create' => 'Create',
                'customers.edit' => 'Ban / Unban',
                'customers.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'orders',
            'label' => 'Orders',
            'group' => 'Sales',
            'route' => 'admin.orders.index',
            'permission' => 'orders.view',
            'actions' => [
                'orders.edit' => 'Update status',
            ],
        ],
        [
            'key' => 'coupons',
            'label' => 'Coupons',
            'group' => 'Sales',
            'route' => 'admin.coupons.index',
            'permission' => 'coupons.view',
            'actions' => [
                'coupons.create' => 'Create',
                'coupons.edit' => 'Edit',
                'coupons.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'users',
            'label' => 'Users',
            'group' => 'People',
            'route' => 'admin.users.index',
            'permission' => 'users.view',
            'actions' => [
                'users.create' => 'Create',
                'users.edit' => 'Edit',
                'users.credentials' => 'Update credentials',
                'users.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'site-setup',
            'label' => 'Site Setup',
            'group' => 'System',
            'route' => 'admin.site-setup.index',
            'permission' => 'site-setup.view',
            'actions' => [
                'site-setup.edit' => 'Edit',
            ],
        ],
        [
            'key' => 'pages',
            'label' => 'Pages',
            'group' => 'System',
            'route' => 'admin.pages.index',
            'permission' => 'pages.view',
            'actions' => [
                'pages.create' => 'Create',
                'pages.edit' => 'Edit',
                'pages.delete' => 'Delete',
            ],
        ],
        [
            'key' => 'settings',
            'label' => 'Settings',
            'group' => 'System',
            'route' => 'admin.settings.index',
            'permission' => 'settings.view',
            'actions' => [
                'settings.edit' => 'Edit',
            ],
        ],
    ],
];
