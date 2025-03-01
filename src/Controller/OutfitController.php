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
        $clothingItems = $wardrobe->getItems();

        return $this->render('outfits.html.twig', [
            'outfits' => $outfits,
            'clothingItems' => $clothingItems,
        ]);
    }

    #[Route('/outfits/create', name: 'create_outfit', methods: ['POST'])]
    public function createOutfit(Request $request, EntityManagerInterface $em, ClothingItemRepository $clothingItemRepository)
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
        $isPublic = $request->get('public') === 'on';
        $outfit->setPublic($isPublic);        $outfit->setCreatedAt(new \DateTime());
        $outfitName = $request->get('name');
        $selectedItems = $request->get('items', []);

        $outfit->setName($outfitName);

        foreach ($selectedItems as $itemId) {
            $item = $clothingItemRepository->find($itemId);
            if ($item) {
                $outfit->addItem($item);
            }
        }

        $em->persist($outfit);
        $em->flush();

        return $this->redirectToRoute('outfits_list');
    }


    #[Route('/public-outfits', name: 'public_outfits')]
    public function index(OutfitRepository $outfitRepository, Request $request)
    {
        $queryBuilder = $outfitRepository->createQueryBuilder('o')
            ->where('o.public = :public')
            ->setParameter('public', true);

        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder->andWhere('o.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $outfits = $queryBuilder->getQuery()->getResult();

        return $this->render('public_outfits.html.twig', [
            'outfits' => $outfits,
            'search' => $search,
        ]);
    }
}
