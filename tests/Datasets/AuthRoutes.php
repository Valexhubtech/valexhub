<?php

dataset('authroutes', function () {
    return [
        '/',
        '/blog',
        '/dashboard',
        '/notifications',
        '/settings/profile',
        '/settings/security',
        '/settings/api',
        '/settings/subscription',
        '/settings/invoices',

        '/admin',
        '/admin/users',
        '/admin/roles',
        '/admin/permissions',
        '/admin/permissions/create',
        '/admin/plans',
        '/admin/posts',
        '/admin/media',
        '/admin/pages',
        '/admin/categories',
        '/admin/changelogs',
        '/admin/themes',
        '/admin/plugins',
        '/admin/settings',
    ];
});
