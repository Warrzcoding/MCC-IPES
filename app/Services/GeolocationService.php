<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    /**
     * Get geolocation data for an IP address
     * Note: For production, ensure you pass the CLIENT IP, not server IP
     *
     * @param string $ipAddress
     * @return array
     */
    public function getLocationData(string $ipAddress): array
    {
        // Skip localhost and private IPs
        if ($this->isPrivateOrLocalhost($ipAddress)) {
            return [
                'latitude' => null,
                'longitude' => null,
                'location' => 'Local/Private Network'
            ];
        }

        // Log for debugging
        Log::debug("GeolocationService: Fetching location for IP {$ipAddress}");

        // Cache key for this IP
        $cacheKey = "geolocation_{$ipAddress}";
        
        // Check cache first (cache for 24 hours)
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Try multiple services for better reliability
            $locationData = $this->tryIpApiCo($ipAddress) 
                         ?? $this->tryIpInfo($ipAddress) 
                         ?? $this->getDefaultLocationData();

            // Cache the result
            Cache::put($cacheKey, $locationData, now()->addHours(24));

            return $locationData;

        } catch (\Exception $e) {
            Log::warning("Geolocation service failed for IP {$ipAddress}: " . $e->getMessage());
            return $this->getDefaultLocationData();
        }
    }

    /**
     * Try ipapi.co service
     */
    private function tryIpApiCo(string $ipAddress): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://ipapi.co/{$ipAddress}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['latitude'], $data['longitude'])) {
                    return [
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'location' => $this->formatLocation($data)
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::debug("ipapi.co failed for {$ipAddress}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Try ipinfo.io service (free tier: 50k requests/month)
     */
    private function tryIpInfo(string $ipAddress): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://ipinfo.io/{$ipAddress}/json");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['loc'])) {
                    [$latitude, $longitude] = explode(',', $data['loc']);
                    
                    return [
                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude,
                        'location' => $this->formatLocationFromIpInfo($data)
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::debug("ipinfo.io failed for {$ipAddress}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Format location string from ipapi.co data
     */
    private function formatLocation(array $data): string
    {
        $parts = [];
        
        if (!empty($data['city'])) {
            $parts[] = $data['city'];
        }
        
        if (!empty($data['region'])) {
            $parts[] = $data['region'];
        }
        
        if (!empty($data['country_name'])) {
            $parts[] = $data['country_name'];
        }

        return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
    }

    /**
     * Format location string from ipinfo.io data
     */
    private function formatLocationFromIpInfo(array $data): string
    {
        $parts = [];
        
        if (!empty($data['city'])) {
            $parts[] = $data['city'];
        }
        
        if (!empty($data['region'])) {
            $parts[] = $data['region'];
        }
        
        if (!empty($data['country'])) {
            $parts[] = $data['country'];
        }

        return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
    }

    /**
     * Check if IP is private or localhost
     */
    private function isPrivateOrLocalhost(string $ipAddress): bool
    {
        // Check for localhost
        if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        // Check for private IP ranges
        return !filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Get default location data when services fail
     */
    private function getDefaultLocationData(): array
    {
        return [
            'latitude' => null,
            'longitude' => null,
            'location' => 'Unknown Location'
        ];
    }
}