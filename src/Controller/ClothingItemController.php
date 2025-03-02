<?php

// src/Controller/ClothingItemController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ClothingItem;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClothingItemRepository;

class ClothingItemController extends AbstractController
{
    

    #[Route('/clothing/{id}/delete', name: 'delete_clothing_item', methods: ['POST'])]
    public function deleteClothingItem($id, EntityManagerInterface $entityManager, ClothingItemRepository $clothingItemRepository)
    {
        $clothingItem = $clothingItemRepository->find($id);

        if ($clothingItem) {
            $entityManager->remove($clothingItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('wardrobe_list');
    }

}