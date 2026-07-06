<?php

namespace Adereksisusanto\FilamentShlink\Models;

use Illuminate\Database\Eloquent\Model;
use Shlinkio\Shlink\SDK\Tags\Model\TagWithStats;

class Tag extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'shlink_tags';
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getKeyName(): string
    {
        return 'name';
    }

    public static function fromShlinkTagWithStats(TagWithStats $tagStats): self
    {
        $stats = TagStats::fromShlinkTagWithStats($tagStats);

        $model = new static();
        $model->forceFill([
            'name' => $tagStats->tag,
            'shortUrlsCount' => $stats->shortUrlsCount,
            'totalVisits' => $stats->totalVisits,
        ]);

        return $model;
    }
}
