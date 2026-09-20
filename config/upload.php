<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chunk Size for Uploads
    |--------------------------------------------------------------------------
    |
    | Default chunk size in bytes. 2 MB is optimal for mobile networks.
    |
    */
    'chunk_size' => env('UPLOAD_CHUNK_SIZE', 2 * 1024 * 1024), // 2 MB

    /*
    |--------------------------------------------------------------------------
    | Maximum Allowed File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size in bytes per photo (e.g. 25 MB).
    |
    */
    'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 25 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types and Extensions
    |--------------------------------------------------------------------------
    */
    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    'allowed_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Disks
    |--------------------------------------------------------------------------
    */
    'photo_disk' => env('UPLOAD_PHOTO_DISK', 'public'),
    'temp_disk' => env('UPLOAD_TEMP_DISK', 'local'),
    'photo_directory' => 'surveys/photos',
    'temp_directory' => 'chunks',
];
