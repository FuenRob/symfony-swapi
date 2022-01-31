<?php

namespace App\Service;

use App\Entity\Planet;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class SwapiService
{
    protected $URL = 'https://swapi.dev/api/planets/';

    /**
     * Gets the planet by id
     *
     * @param int $id
     * @return array
     */
    public function getPlanetById(int $id): array
    {
        ini_set('memory_limit', '-1');
        try {
            $httpClient = HttpClient::create();
            $response = $httpClient->request('GET', $this->URL.$id);
            $content = $response->toArray();
        } catch (\Throwable $th) {
            return[];
        }

        return [
            "id" => $id,
            "name" => $content['name'],
            "rotation_period" => $content['rotation_period'],
            "orbital_period" => $content['orbital_period'],
            "diameter" => $content['orbital_period'],
            "films_count" => count($content['films']),
            "created" => $content['created'],
            "edited" => $content['edited'],
            "url" => $content['url']
        ];
    }
}