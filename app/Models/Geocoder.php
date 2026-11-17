<?php

declare(strict_types=1);

namespace App\Models;

class Geocoder
{
    public static function search(string $query): array
    {
        $config = config('delivery');
        if (!$config['geocoding_enabled'] || !function_exists('curl_init')) {
            throw new \RuntimeException('Geocoding unavailable');
        }
        $endpoint = $config['geocoding_url'];
        if (!str_starts_with($endpoint, 'https://') || str_contains(strtolower($endpoint), 'nominatim.openstreetmap.org')) {
            throw new \RuntimeException('An HTTPS Photon endpoint is required');
        }
        $dir = BASE_PATH . '/storage/cache/geocoding';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Cache unavailable');
        }
        $cacheFile = $dir . '/' . hash('sha256', $endpoint . '|VN|' . mb_strtolower($query)) . '.json';
        if (is_file($cacheFile) && filemtime($cacheFile) > time() - 21600) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)) return $cached;
        }
        // Giới hạn chung cho mọi session trên máy: tối đa một lần bắt đầu gọi mỗi 1,3 giây.
        $lock = @fopen($dir . '/request-clock', 'c+');
        if (!$lock || !flock($lock, LOCK_EX)) throw new \RuntimeException('Rate limiter unavailable');
        $previous = (float) stream_get_contents($lock);
        $now = microtime(true);
        if ($now - $previous < 1.3) {
            flock($lock, LOCK_UN); fclose($lock);
            throw new \RuntimeException('Slow down', 429);
        }
        rewind($lock); ftruncate($lock, 0); fwrite($lock, (string) $now); fflush($lock);
        flock($lock, LOCK_UN); fclose($lock);

        $url = rtrim($endpoint, '/') . '/?' . http_build_query(['q' => $query, 'countrycode' => 'VN', 'limit' => 5]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'Yumio-CNWeb/1.0 (interactive address search)',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if ($body === false || $status !== 200 || strlen($body) > 1024 * 1024) {
            throw new \RuntimeException('Provider unavailable', $status === 429 ? 429 : 0);
        }
        $data = json_decode($body, true);
        if (!is_array($data['features'] ?? null)) throw new \RuntimeException('Invalid response');
        $results = [];
        foreach (array_slice($data['features'], 0, 5) as $feature) {
            if (!is_array($feature)) continue;
            $props = is_array($feature['properties'] ?? null) ? $feature['properties'] : [];
            $geometry = is_array($feature['geometry'] ?? null) ? $feature['geometry'] : [];
            $point = $geometry['coordinates'] ?? [];
            $text = fn (string $key): string => is_scalar($props[$key] ?? null) ? trim((string) $props[$key]) : '';
            if (strtoupper($text('countrycode')) !== 'VN' || ($geometry['type'] ?? '') !== 'Point'
                || !is_array($point) || count($point) !== 2 || !is_numeric($point[0]) || !is_numeric($point[1])
                || abs((float) $point[0]) > 180 || abs((float) $point[1]) > 90) continue;
            $parts = [$text('name'), trim($text('housenumber') . ' ' . $text('street'))];
            foreach (['locality', 'district', 'county', 'city', 'state'] as $key) $parts[] = $text($key);
            $parts = array_values(array_unique(array_filter($parts, fn ($part) => is_string($part) && $part !== '')));
            if (!$parts) continue;
            $label = mb_substr(implode(', ', $parts), 0, 255);
            if (isset($results[$label])) continue;
            $results[$label] = ['label' => $label, 'name' => $text('name') ?: $parts[0], 'lat' => (float) $point[1], 'lng' => (float) $point[0]];
        }
        $results = array_values($results);
        @file_put_contents($cacheFile, json_encode($results, JSON_UNESCAPED_UNICODE), LOCK_EX);
        return $results;
    }
}
