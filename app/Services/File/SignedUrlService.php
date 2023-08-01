<?php

namespace App\Services\File;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SignedUrlService
{
    private $file_id, $convert_id;

    public function __construct($file_id, $convert_id)
    {
        $this->file_id = $file_id;
        $this->convert_id = $convert_id;
    }

    public static function make($file_id, $convert_id)
    {
        return new self($file_id, $convert_id);
    }

    /**
     * Generate the cached URL.
     *
     * @return string|null The generated URL, or null if the cache is empty.
     */
    public function generate(): ?string
    {
        $cache_key = $this->getCachedUrl();

        if (!$cache_key) {
            $expiresAt = $this->getExpireDefault();

            $signedUrl = URL::temporarySignedRoute('download-link', $expiresAt, [
                'id' => $this->file_id,
                'convert_id' => $this->convert_id
            ]);

            $this->makeNewCache($signedUrl);
        }

        return $this->getCachedUrl();
    }

    /**
     * Get the cached URL.
     *
     * @return string|null The cached URL, or null if not found in cache.
     */
    private function getCachedUrl(): ?string
    {
        return Cache::get($this->getCacheKey());
    }

    /**
     * @param string $signedUrl
     * @return void
     */
    private function makeNewCache(string $signedUrl): void
    {
        $expiresAt = $this->getExpireDefault()->subMinute(10);

        Cache::put($this->getCacheKey(), $signedUrl, $expiresAt);
    }

    /**
     * Get the default expiration time.
     *
     * @return \Illuminate\Support\Carbon
     */
    private function getExpireDefault(): \Illuminate\Support\Carbon
    {
        return now()->addHour(2);
    }

    /**
     * Get the cache key.
     *
     * @return string The cache key.
     */
    private function getCacheKey(): string
    {
        return 'dl_' . auth()->user()->id . ':' . $this->file_id . '_' . $this->convert_id;
    }
}
