<?php

namespace App\Controller;

use App\Form\DiabetesPredictionType;
use App\Service\DiabetesPredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
{
    public function __construct(
        private DiabetesPredictionService $predictionService
    ) {}

    #[Route('/predict', name: 'app_prediction', methods: ['GET', 'POST'])]  // ✅ méthodes explicites
    public function index(Request $request): Response
    {
        $form = $this->createForm(DiabetesPredictionType::class);
        $form->handleRequest($request);

        $result    = null;
        $error     = null;
        $apiOnline = $this->predictionService->checkHealth();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // ✅ Cast explicite des types pour Flask
            $response = $this->predictionService->predict([
                'pregnancies'       => (int)   $data['pregnancies'],
                'glucose'           => (float) $data['glucose'],
                'blood_pressure'    => (float) $data['blood_pressure'],
                'skin_thickness'    => (float) $data['skin_thickness'],
                'insulin'           => (float) $data['insulin'],
                'bmi'               => (float) $data['bmi'],
                'diabetes_pedigree' => (float) $data['diabetes_pedigree'],
                'age'               => (int)   $data['age'],
            ]);

            if ($response['success']) {
                $result = $response['data'];
            } else {
                $error = $response['error'];
            }
        }

        return $this->render('prediction/index.html.twig', [
            'form'      => $form->createView(),
            'result'    => $result,
            'error'     => $error,
            'apiOnline' => $apiOnline,
        ]);
    }

    #[Route('/predictPlot', name: 'app_prediction_plot')]
    public function plot(): Response
    {
        return $this->render('prediction/plot.html.twig', [
            'controller_name' => 'PredictionController',
        ]);
    }
}
