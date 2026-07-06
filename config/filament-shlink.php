<?php

return [
    /*
     * Shlink server base URL (e.g. https://shlink.yourdomain.com).
     * Fallback used when the authenticated user has no saved config.
     */
    'server_url' => env('SHLINK_SERVER_URL', ''),

    /*
     * Shlink API key with admin role.
     * Fallback used when the authenticated user has no saved config.
     */
    'api_key' => env('SHLINK_API_KEY', ''),

    /*
     * Database table prefix for the per-user config table.
     * The full table name will be: {table_prefix}_configs.
     */
    'table_prefix' => 'fs',
];
