<?php

namespace App\Controller;

use App\Entity\ClothingItem;
use App\Entity\User;
use App\Entity\Wardrobe;
use App\Repository\ClothingItemRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class WardrobeController extends AbstractController
{

    #[Route('/wardrobe/add', name: 'add_to_wardrobe', methods: ['POST'])]
    public function addToWardrobe(Request $request, ClothingItemRepository $clothingItemRepository, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }


        $name = $request->request->get('name');
        $categorie = $request->request->get('categorie');
        $image = $request->request->get('image');

        if (!$name || !$categorie || !$image) {
            $this->addFlash('error', 'Données invalides.');
            return $this->redirectToRoute('detection_page');
        }

        $existingItem = $clothingItemRepository->findOneBy(['name' => $name, 'image' => $image]);

        if ($existingItem) {
            $clothingItem = $existingItem;
        } else {
            $clothingItem = new ClothingItem();
            $clothingItem->setName($name);
            $clothingItem->setImage($image);

            $categoryRepository = $entityManager->getRepository(Category::class);
            $category = $categoryRepository->findOneBy(['name' => $categorie]);

            if ($category) {
                $clothingItem->addCategory($category);
            }

            $entityManager->persist($clothingItem);
            $entityManager->flush();
        }

        $wardrobe = $user->getWardrobe();
        if (!$wardrobe) {
            $wardrobe = new Wardrobe();
            $wardrobe->setOwner($user);
            $entityManager->persist($wardrobe);
            $entityManager->flush();
        }

        $wardrobe->addItem($clothingItem);
        $entityManager->flush();

        $this->addFlash('success', 'L\'élément a été ajouté à votre garde-robe.');
        return $this->redirectToRoute('home');

    }


    #[Route('/wardrobe', name: 'wardrobe_list')]
    public function viewWardrobe(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }

        $wardrobe = $user->getWardrobe();
        if (!$wardrobe) {
            return $this->render('wardrobe.html.twig', [
                'clothingItems' => [],
            ]);
        }

        $clothingItems = $wardrobe->getItems();

        return $this->render('wardrobe.html.twig', [
            'clothingItems' => $clothingItems,
        ]);
    }

}
