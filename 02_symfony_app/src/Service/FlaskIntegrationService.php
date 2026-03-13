<?php

namespace App\Service;
use Symfony\Component\HttpClient\HttpClient;

class FlaskIntegrationService
{
    private $flaskEndpoint;

    public function __construct(string $flaskEndpoint)
    {
        $this->flaskEndpoint = $flaskEndpoint;
    }

    public function executeFlaskAPI(array $data): array
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request(
            'POST',
            $this->flaskEndpoint.'/predict', // Replace with the appropriate endpoint on your Flask application
            [
                'json' => $data,
            ]
        );

        return $response->toArray();
    }
}