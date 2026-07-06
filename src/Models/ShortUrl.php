<?php

namespace Adereksisusanto\FilamentShlink\Models;

use Illuminate\Database\Eloquent\Model;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrl as ShlinkShortUrl;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;

class ShortUrl extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $table = 'shlink_short_urls';
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function fromShlinkShortUrl(ShlinkShortUrl $url): self
    {
        $model = new static();
        $model->forceFill([
            'shortCode' => $url->shortCode,
            'shortUrl' => $url->shortUrl,
            'longUrl' => $url->longUrl,
            'title' => $url->title,
            'domain' => $url->domain,
            'crawlable' => $url->crawlable,
            'forwardQuery' => $url->forwardQuery,
            'tags' => $url->tags,
            'meta' => $url->meta,
            'visitsSummary' => $url->visitsSummary,
            'dateCreated' => $url->dateCreated,
            'hasRedirectRules' => $url->hasRedirectRules ?? null,
        ]);

        return $model;
    }

    public function getKeyName(): string
    {
        return 'shortCode';
    }

    public function identifier(): ShortUrlIdentifier
    {
        return ShortUrlIdentifier::fromShortCode($this->shortCode);
    }
}
