<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TinyUrlService
{
  protected string $baseUrl = 'https://tinyurl.com/api-create.php';

  /**
   * Shorten a URL via TinyURL. Falls back to the original URL on any failure
   * so a shortening outage never blocks message sending.
   */
  public function shorten(string $url): string
  {
    return Cache::rememberForever('tinyurl:' . md5($url), function () use ($url) {
      try {
        $response = Http::timeout(5)->get($this->baseUrl, ['url' => $url]);
        $shortUrl = trim($response->body());

        if ($response->successful() && str_starts_with($shortUrl, 'http')) {
          return $shortUrl;
        }

        Log::warning('TinyURL shortening failed, using original URL.', [
          'url' => $url,
          'response' => $shortUrl,
        ]);
      } catch (\Exception $e) {
        Log::warning('TinyURL shortening error, using original URL.', [
          'url' => $url,
          'error' => $e->getMessage(),
        ]);
      }

      return $url;
    });
  }
}
