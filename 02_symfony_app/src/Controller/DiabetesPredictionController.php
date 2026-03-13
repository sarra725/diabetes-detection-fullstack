<?php

namespace App\Controller;

use App\Form\DiabetesPredictionType;
use App\Service\DiabetesPredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prediction', name: 'prediction_')]
class DiabetesPredictionController extends AbstractController
{
    public function __construct(
        private DiabetesPredictionService $predictionService
    ) {}

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(DiabetesPredictionType::class);
        $form->handleRequest($request);

        $result = null;
        $error = null;
        $apiOnline = $this->predictionService->checkHealth();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $response = $this->predictionService->predict([
                'pregnancies'       => $data['pregnancies'],
                'glucose'           => $data['glucose'],
                'blood_pressure'    => $data['blood_pressure'],
                'skin_thickness'    => $data['skin_thickness'],
                'insulin'           => $data['insulin'],
                'bmi'               => $data['bmi'],
                'diabetes_pedigree' => $data['diabetes_pedigree'],
                'age'               => $data['age'],
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
}
