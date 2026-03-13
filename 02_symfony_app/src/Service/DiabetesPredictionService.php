<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DiabetesPredictionService
{
    private string $flaskApiUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
        string $flaskApiUrl = 'http://127.0.0.1:5000'  // ✅ fixé : 127.0.0.1 au lieu de localhost
    ) {
        $this->flaskApiUrl = $flaskApiUrl;
    }

    /**
     * Predict diabetes risk from patient data
     */
    public function predict(array $patientData): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->flaskApiUrl . '/predict', [
                'headers' => [
                    'Content-Type' => 'application/json',  // ✅ ajouté explicitement
                    'Accept'       => 'application/json',
                ],
                'json'    => $patientData,
                'timeout' => 10,
            ]);

            $statusCode = $response->getStatusCode();
            $content    = $response->toArray(false);

            if ($statusCode !== 200) {
                return [
                    'success' => false,
                    'error'   => $content['error'] ?? 'Erreur inconnue de l\'API',
                ];
            }

            return [
                'success' => true,
                'data'    => $content,
            ];

        } catch (TransportExceptionInterface $e) {
            return [
                'success' => false,
                'error'   => 'Impossible de joindre le service de prédiction. Vérifiez que l\'API Flask est démarrée sur http://127.0.0.1:5000',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Flask API is running
     */
    public function checkHealth(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->flaskApiUrl . '/health', [
                'timeout' => 3,
            ]);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
