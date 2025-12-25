<?php

return [

    'default' => env('FILESYSTEM_DISK', 'minio'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'minio' => [
            'driver' => 's3',
            'key' => env('MINIO_KEY'),
            'secret' => env('MINIO_SECRET'),
            'region' => env('MINIO_REGION', 'us-east-1'),
            'bucket' => env('MINIO_BUCKET', 'arsip-pusri'),
            'endpoint' => env('MINIO_ENDPOINT', 'http://localhost:9000'),
            'use_path_style_endpoint' => env('MINIO_USE_PATH_STYLE_ENDPOINT', true),
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
