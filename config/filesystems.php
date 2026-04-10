<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'private',
            'permissions' => [
                'file' => [
                    'public' => 0o644,
                    'private' => 0o600,
                ],
                'dir' => [
                    'public' => 0o755,
                    'private' => 0o700,
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'permissions' => [
                'file' => [
                    'public' => 0o644,
                    'private' => 0o600,
                ],
                'dir' => [
                    'public' => 0o755,
                    'private' => 0o700,
                ],
            ],
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
