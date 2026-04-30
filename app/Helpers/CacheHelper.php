<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Check if the current cache driver supports tagging
     */
    public static function supportsTagging(): bool
    {
        return method_exists(Cache::getStore(), 'tags');
    }

    /**
     * Get cache with optional tags support
     *
     * @param  string|array  $tags
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($tags, string $key, $default = null)
    {
        if (static::supportsTagging()) {
            return Cache::tags($tags)->get($key, $default);
        }

        // Fallback: use key with tag prefix
        $prefixedKey = static::getPrefixedKey($tags, $key);

        return Cache::get($prefixedKey, $default);
    }

    /**
     * Put cache with optional tags support
     *
     * @param  string|array  $tags
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     */
    public static function put($tags, string $key, $value, $ttl = null): bool
    {
        if (static::supportsTagging()) {
            return Cache::tags($tags)->put($key, $value, $ttl);
        }

        // Fallback: use key with tag prefix
        $prefixedKey = static::getPrefixedKey($tags, $key);

        return Cache::put($prefixedKey, $value, $ttl);
    }

    /**
     * Remember cache with optional tags support
     *
     * @param  string|array  $tags
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return mixed
     */
    public static function remember($tags, string $key, $ttl, \Closure $callback)
    {
        if (static::supportsTagging()) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }

        // Fallback: use key with tag prefix
        $prefixedKey = static::getPrefixedKey($tags, $key);

        return Cache::remember($prefixedKey, $ttl, $callback);
    }

    /**
     * Flush cache tags with optional tags support
     *
     * @param  string|array  $tags
     */
    public static function flush($tags): bool
    {
        if (static::supportsTagging()) {
            return Cache::tags($tags)->flush();
        }

        // Fallback: cannot flush by tags, so clear all cache
        // This is a limitation of drivers that don't support tagging
        return Cache::flush();
    }

    /**
     * Create a prefixed key from tags
     *
     * @param  string|array  $tags
     */
    protected static function getPrefixedKey($tags, string $key): string
    {
        $tagString = is_array($tags) ? implode(':', $tags) : $tags;

        return "{$tagString}:{$key}";
    }
}
