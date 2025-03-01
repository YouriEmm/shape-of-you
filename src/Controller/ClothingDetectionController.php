<?php

namespace App\Controller;

use App\Entity\DetectedClothing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\OpenAIService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class ClothingDetectionController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SessionInterface $session): Response
    {
        $detectedClothings = $session->get('detected_clothing', []);
    
        return $this->render('detection.html.twig', [
            'detected_clothing' => $detectedClothings
        ]);
    }

    #[Route('/detect-clothing', name: 'detect_clothing', methods: ['POST'])]
    public function detectClothing(
        Request $request,
        OpenAIService $openAIService,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {
    
        $file = $request->files->get('image');
        if (!$file instanceof UploadedFile) {
            return $this->json(['error' => 'Aucune image fournie'], Response::HTTP_BAD_REQUEST);
        }
    
        $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
    
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }
    
        $fileName = uniqid() . '.' . $file->guessExtension();
    
        try {
            $file->move($uploadDirectory, $fileName);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de l\'upload'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        $imageContent = file_get_contents($uploadDirectory . '/' . $fileName);
        $imageBase64 = base64_encode($imageContent);
        $rawResponse = $openAIService->detectClothingWithVision($imageBase64);
    
        $cleanedResponse = trim(str_replace(["```json", "```", "\n"], "", $rawResponse));
        $jsonResponse = str_replace("'", '"', $cleanedResponse);
        $detectedItems = json_decode($jsonResponse, true);
    
        if (!is_array($detectedItems) || empty($detectedItems)) {
            return $this->json(['error' => 'Aucun vêtement détecté'], Response::HTTP_BAD_REQUEST);
        }
    
        $detectedClothings = [];
        foreach ($detectedItems as $key => $item) {
            if (!is_array($item) || !isset($item['Nom'])) {
                continue;
            }
    
            $detectedClothing = new DetectedClothing();
            $detectedClothing->setImagePath('/uploads/' . $fileName);
            $detectedClothing->setDetectedItems([$item]);
    
            $entityManager->persist($detectedClothing);
            $detectedClothings[] = [
                'nom' => $item['Nom'],
                'categorie' => $item['categorie'],
                'marque' => $item['Marque'],
                'image_path' => $detectedClothing->getImagePath(),
            ];
        }
    
        $session->set('detected_clothing', $detectedClothings);
        $entityManager->flush();
    
        return $this->redirectToRoute('home');

    }
    
}
