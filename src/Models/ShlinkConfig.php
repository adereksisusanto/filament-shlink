<?php

namespace Adereksisusanto\FilamentShlink\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShlinkConfig extends Model
{
    protected static string $tablePrefix = 'fs';

    protected $fillable = [
        'user_id',
        'server_url',
        'api_key',
    ];

    public static function setTablePrefix(string $prefix): void
    {
        static::$tablePrefix = $prefix;
    }

    public static function getTablePrefix(): string
    {
        return static::$tablePrefix;
    }

    public function getTable()
    {
        return static::$tablePrefix . '_configs';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
