<?php

// src/Controller/OutfitController.php

namespace App\Controller;

use App\Repository\OutfitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;    
use App\Entity\Outfit;    
use App\Repository\ClothingItemRepository;
use App\Entity\ClothingItem;
use App\Service\OpenAIService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\OutfitType;

class OutfitController extends AbstractController
{
    

    #[Route('/outfits', name: 'outfits_list')]
    public function listOutfits(OutfitRepository $outfitRepository,ClothingItemRepository $clothingItemRepository)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }

        $outfits = $outfitRepository->findBy(['owner' => $user]);
        
        $wardrobe = $user->getWardrobe();
        if (!$wardrobe) {
            return $this->render('outfits.html.twig', [
                'outfits' => [],
                'clothingItems' => [],
            ]);
        }
        

        $clothingItems = $wardrobe->getItems();

        return $this->render('outfits.html.twig', [
            'outfits' => $outfits,
            'clothingItems' => $clothingItems,
        ]);
    }

    #[Route('/outfits/create', name: 'create_outfit', methods: ['POST', 'GET'])]
    public function createOutfit(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }
    
        $outfit = new Outfit();
        $outfit->setOwner($user);
        $outfit->setCreatedAt(new \DateTime());
    
        $form = $this->createForm(OutfitType::class, $outfit, [
            'csrf_protection' => false,
        ]);

        $data = $request->request->all();
        if ($data) {
            $form->submit($data, false);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($outfit);
                $em->flush();
                
                return $this->redirectToRoute('outfits_list');
            }
        }
    
        return $this->redirectToRoute('outfits_list');

    }
    


    #[Route('/public-outfits', name: 'public_outfits')]
    public function index(OutfitRepository $outfitRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $queryBuilder = $outfitRepository->createQueryBuilder('o')
            ->where('o.public = :public')
            ->setParameter('public', true);
    
        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder->andWhere('o.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        $categoryFilter = $request->query->get('category');
        if ($categoryFilter) {
            $queryBuilder
                ->join('o.items', 'c')
                ->andWhere('c.category = :category')
                ->setParameter('category', $categoryFilter);
        }
    
        $outfits = $queryBuilder->getQuery()->getResult();
    
        $categories = $entityManager->createQueryBuilder()
            ->select('DISTINCT c.category')
            ->from('App\Entity\ClothingItem', 'c')
            ->where('c.category IS NOT NULL')
            ->getQuery()
            ->getResult();
    
        return $this->render('public_outfits.html.twig', [
            'outfits' => $outfits,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'categories' => $categories,
        ]);
    }
    
    


    #[Route('/outfit/generate', name: 'generate_outfit_ai', methods: ['POST'])]
    public function generateOutfitAI(Request $request, OpenAIService $openAIService, EntityManagerInterface $entityManager) {
    
        $description = $request->request->get('description');
    
        if (strlen($description) < 50) {
            return new JsonResponse(['error' => 'La description doit contenir au moins 50 caractères.'], 400);
        }
    
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }
    
        $wardrobeItems = $user->getWardrobe()->getItems()->toArray();
        

        if (!$wardrobeItems) {
            return new JsonResponse(['error' => 'Aucun vêtement trouvé dans la garde-robe.'], 400);
        }
    
        $outfitData = $openAIService->generateOutfit($description, $wardrobeItems);

        $outfitData = str_replace("'", '"', $outfitData);

        $outfitData = json_decode($outfitData, true);

        if ($outfitData === null) {
            return new JsonResponse(['error' => 'Erreur de décryptage des données de l\'outfit.'], 500);
        }

        $outfit = new Outfit();
        $outfit->setName($outfitData['outfit_name']);
        $outfit->setOwner($user);
        $outfit->setCreatedAt(new \DateTime());
        $outfit->setPublic(false);

        foreach ($outfitData['items'] as $itemData) {
            $clothingItem = $entityManager->getRepository(ClothingItem::class)->find($itemData['id']);
            if ($clothingItem) {
                $outfit->addItem($clothingItem);
            }
        }

        $entityManager->persist($outfit);
        $entityManager->flush();

        return $this->redirectToRoute('outfits_list');

    }

    #[Route('/outfit/{id}/toggle-public', name: 'toggle_outfit_public', methods: ['POST'])]

    public function toggleOutfitPublic(Outfit $outfit, EntityManagerInterface $entityManager)
    {
        $outfit->setPublic(!$outfit->isPublic());

        $entityManager->persist($outfit);
        $entityManager->flush();

        return $this->redirectToRoute('outfits_list');
    }

    #[Route('/outfit/{id}/delete', name: 'delete_outfit', methods: ['POST'])]
    public function deleteClothingItem($id, EntityManagerInterface $entityManager, OutfitRepository $outfitRepository)
    {
        $clothingItem = $outfitRepository->find($id);

        if ($clothingItem) {
            $entityManager->remove($clothingItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('outfits_list');
    }

    #[Route('/outfit/{id}/deleteAdmin', name: 'delete_outfit_admin', methods: ['POST'])]
    public function deleteClothingItemAdmin($id, EntityManagerInterface $entityManager, OutfitRepository $outfitRepository): Response
    {
        $clothingItem = $outfitRepository->find($id);

        $userId = $clothingItem->getOwner()->getId(); 
        $entityManager->remove($clothingItem);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_show', ['id' => $userId]);
        
    }
    

}
