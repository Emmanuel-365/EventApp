<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected string $apiKey;
    protected Client $httpClient;

    public function __construct()
    {
        $this->apiKey = Config::get('services.Maps.api_key');
        if (empty($this->apiKey)) {
            throw new \Exception("Google Maps API key is not configured. Please set Maps_API_KEY in your .env file.");
        }
        $this->httpClient = new Client();
    }

    /**
     * Retourne la clé API Google Maps.
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Géocode une adresse textuelle en coordonnées latitude et longitude.
     * Utile si vous avez une adresse et que vous voulez la convertir en points sur la carte.
     * (Bien que l'autocomplétion côté client gère souvent cela directement).
     *
     * @param string $address L'adresse à géocoder.
     * @return array|null Tableau ['lat' => float, 'lng' => float] ou null si non trouvé.
     * @throws GuzzleException
     */
    public function geocodeAddress(string $address): ?array
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?";
        $params = [
            'address' => $address,
            'key' => $this->apiKey,
        ];

        try {
            $response = $this->httpClient->get($url, ['query' => $params]);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng']
                ];
            }
            Log::warning('Geocoding failed for address: ' . $address . ' Status: ' . ($data['status'] ?? 'N/A'));
            return null;
        } catch (\Exception $e) {
            Log::error('Error during geocoding address: ' . $e->getMessage(), ['address' => $address]);
            return null;
        }
    }

    /**
     * Géocode des coordonnées latitude et longitude en adresse textuelle (géocodage inversé).
     * Très utile pour obtenir l'adresse lisible d'un point sélectionné sur la carte.
     *
     * @param float $latitude La latitude.
     * @param float $longitude La longitude.
     * @return string|null L'adresse formatée ou null si non trouvée.
     * @throws GuzzleException
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?";
        $params = [
            'latlng' => "{$latitude},{$longitude}",
            'key' => $this->apiKey,
        ];

        try {
            $response = $this->httpClient->get($url, ['query' => $params]);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                return $data['results'][0]['formatted_address'];
            }
            Log::warning('Reverse geocoding failed for lat/lng: ' . $latitude . ',' . $longitude . ' Status: ' . ($data['status'] ?? 'N/A'));
            return null;
        } catch (\Exception $e) {
            Log::error('Error during reverse geocoding: ' . $e->getMessage(), ['lat' => $latitude, 'lng' => $longitude]);
            return null;
        }
    }
}
