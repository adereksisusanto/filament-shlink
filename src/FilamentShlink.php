<?php

namespace Adereksisusanto\FilamentShlink;

use Adereksisusanto\FilamentShlink\Models\ShlinkConfig;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use Shlinkio\Shlink\SDK\Builder\ShlinkClientBuilder;
use Shlinkio\Shlink\SDK\Config\ShlinkConfig as ShlinkSdkConfig;
use Shlinkio\Shlink\SDK\Config\ShlinkConfigInterface;
use Shlinkio\Shlink\SDK\Domains\Model\DomainRedirectsConfig;
use Shlinkio\Shlink\SDK\ShlinkClient;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlCreation;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlEdition;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlIdentifier;
use Shlinkio\Shlink\SDK\ShortUrls\Model\ShortUrlsFilter;
use Shlinkio\Shlink\SDK\Tags\Model\TagRenaming;
use Shlinkio\Shlink\SDK\Tags\Model\TagsFilter;
use Shlinkio\Shlink\SDK\Visits\Model\VisitsFilter;

class FilamentShlink
{
    private array $clients = [];

    public function client(): mixed
    {
        $key = $this->resolveClientKey();

        if (! isset($this->clients[$key])) {
            $this->clients[$key] = $this->buildClient();
        }

        return $this->clients[$key];
    }

    public function setConfig(string $serverUrl, string $apiKey): void
    {
        $key = $this->resolveClientKey();
        $this->clients[$key] = null;

        $guzzle = new GuzzleClient;
        $httpFactory = new HttpFactory;
        $builder = new ShlinkClientBuilder($guzzle, $httpFactory, $httpFactory);
        $config = ShlinkSdkConfig::fromBaseUrlAndApiKey(
            baseUrl: $serverUrl,
            apiKey: $apiKey,
        );

        $this->clients[$key] = new ShlinkClient(
            $builder->buildShortUrlsClient($config),
            $builder->buildVisitsClient($config),
            $builder->buildTagsClient($config),
            $builder->buildDomainsClient($config),
            $builder->buildRedirectRulesClient($config),
        );
    }

    public function isConfigured(): bool
    {
        if (auth()->check()) {
            return ShlinkConfig::where('user_id', auth()->id())->exists();
        }

        return ! empty(config('filament-shlink.server_url')) && ! empty(config('filament-shlink.api_key'));
    }

    public function listShortUrls(?ShortUrlsFilter $filter = null): mixed
    {
        return $filter
            ? $this->client()->listShortUrlsWithFilter($filter)
            : $this->client()->listShortUrls();
    }

    public function getShortUrl(ShortUrlIdentifier $identifier): mixed
    {
        return $this->client()->getShortUrl($identifier);
    }

    public function createShortUrl(ShortUrlCreation $creation): mixed
    {
        return $this->client()->createShortUrl($creation);
    }

    public function editShortUrl(ShortUrlIdentifier $identifier, ShortUrlEdition $edition): mixed
    {
        return $this->client()->editShortUrl($identifier, $edition);
    }

    public function deleteShortUrl(ShortUrlIdentifier $identifier): void
    {
        $this->client()->deleteShortUrl($identifier);
    }

    public function listTags(?TagsFilter $filter = null): array
    {
        return $filter
            ? $this->client()->listTagsWithFilter($filter)
            : $this->client()->listTags();
    }

    public function listTagsWithStats(?TagsFilter $filter = null): mixed
    {
        return $filter
            ? $this->client()->listTagsWithStatsWithFilter($filter)
            : $this->client()->listTagsWithStats();
    }

    public function renameTag(TagRenaming $tagRenaming): void
    {
        $this->client()->renameTag($tagRenaming);
    }

    public function deleteTags(string ...$tags): void
    {
        $this->client()->deleteTags(...$tags);
    }

    public function listDomains(): mixed
    {
        return $this->client()->listDomains();
    }

    public function configureDomainRedirects(DomainRedirectsConfig $redirects): mixed
    {
        return $this->client()->configureDomainRedirects($redirects);
    }

    public function getVisitsOverview(): mixed
    {
        return $this->client()->getVisitsOverview();
    }

    public function listShortUrlVisits(ShortUrlIdentifier $identifier, ?VisitsFilter $filter = null): mixed
    {
        return $filter
            ? $this->client()->listShortUrlVisitsWithFilter($identifier, $filter)
            : $this->client()->listShortUrlVisits($identifier);
    }

    public function deleteShortUrlVisits(ShortUrlIdentifier $identifier): mixed
    {
        return $this->client()->deleteShortUrlVisits($identifier);
    }

    private function resolveClientKey(): string
    {
        return auth()->check() ? 'user_' . auth()->id() : 'guest';
    }

    private function buildClient(): mixed
    {
        $guzzle = new GuzzleClient;
        $httpFactory = new HttpFactory;
        $builder = new ShlinkClientBuilder($guzzle, $httpFactory, $httpFactory);

        $config = $this->resolveConfig();

        return new ShlinkClient(
            $builder->buildShortUrlsClient($config),
            $builder->buildVisitsClient($config),
            $builder->buildTagsClient($config),
            $builder->buildDomainsClient($config),
            $builder->buildRedirectRulesClient($config),
        );
    }

    private function resolveConfig(): ShlinkConfigInterface
    {
        if (auth()->check()) {
            $userConfig = ShlinkConfig::where('user_id', auth()->id())->first();

            if ($userConfig) {
                return ShlinkSdkConfig::fromBaseUrlAndApiKey(
                    baseUrl: $userConfig->server_url,
                    apiKey: $userConfig->api_key,
                );
            }
        }

        return ShlinkSdkConfig::fromBaseUrlAndApiKey(
            baseUrl: config('filament-shlink.server_url'),
            apiKey: config('filament-shlink.api_key'),
        );
    }
}
