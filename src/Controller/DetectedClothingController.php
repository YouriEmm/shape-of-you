<?php

namespace App\Controller;

use App\Entity\DetectedClothing;
use App\Entity\User;
use App\Form\DetectedClothingType;
use App\Form\DetectedClothingTypeImage;
use App\Service\OpenAIService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/detected/clothing')]
final class DetectedClothingController extends AbstractController
{
    #[Route('', name: 'home', methods: ['GET', 'POST'])]
    public function detectClothing(
        Request $request,
        OpenAIService $openAIService,
        EntityManagerInterface $entityManager,
    ) {

        $detectedClothing = new DetectedClothing();

        $detectedItems = [];
        $image_path= '';
    
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $form = $this->createForm(DetectedClothingTypeImage::class, $detectedClothing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            
            if (!$file instanceof UploadedFile) {  
                $this->addFlash('error', "Aucune image fournie");
                return $this->redirectToRoute('home');   
            }
        
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
        
            $fileName = uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($uploadDirectory, $fileName);
            } catch (\Exception $e) {
                $this->addFlash('error', "Erreur lors de l\'upload");
                return $this->redirectToRoute('home');         
            }
        
            $detectedClothing->setImagePath('/uploads/' . $fileName);
            $image_path = '/uploads/' . $fileName;

            $imageContent = file_get_contents($uploadDirectory . '/' . $fileName);
            $imageBase64 = base64_encode($imageContent);
            
            $rawResponse = $openAIService->detectClothingWithVision($imageBase64);
        
            $cleanedResponse = trim(str_replace(["```json", "```", "\n"], "", $rawResponse));
            $jsonResponse = str_replace("'", '"', $cleanedResponse);
            $detectedItems = json_decode($jsonResponse, true);
            
            if (!is_array($detectedItems) || empty($detectedItems) || $detectedItems == null) {
                $this->addFlash('error', "Aucun vêtement détecté");
                return $this->redirectToRoute('home');            
            }

            $detectedClothing->setDetectedItems($detectedItems);
            $detectedClothing->setOwner($user);

            $entityManager->persist($detectedClothing);
            $entityManager->flush();
        }
        
        return $this->render('detection.html.twig', [
            'detectedItems' => $detectedItems,
            'formDetectionImage' => $form->createView(),
            'image_path' => $image_path
        ]);
    }
    

    #[Route('/list', name: 'detected_outfits_list')]
    public function listDetectedOutfits(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $detectedOutfits = $user->getDetectedClothing();
        foreach ($detectedOutfits as $outfit) {
            foreach ($outfit->getDetectedItems() as &$clothing) {
                if (is_string($clothing)) {
                    $decoded = json_decode($clothing, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $clothing = $decoded;
                    }
                }
            }
        }

        return $this->render('detected_outfit_list.html.twig', [
            'detectedOutfits' => $detectedOutfits,
        ]);
    }
}
