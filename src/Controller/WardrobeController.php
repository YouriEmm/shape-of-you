<?php

namespace App\Controller;

use App\Entity\ClothingItem;
use App\Entity\User;
use App\Entity\Wardrobe;
use App\Entity\Brand;
use App\Repository\ClothingItemRepository;
use App\Repository\PartnerRepository;
use App\Entity\Category;
use App\Entity\Partner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class WardrobeController extends AbstractController
{

    #[Route('/wardrobe/add', name: 'add_to_wardrobe', methods: ['POST'])]
    public function addToWardrobe(
        Request $request, 
        ClothingItemRepository $clothingItemRepository, 
        PartnerRepository $brandRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }

        $name = $request->request->get('name');
        $category = $request->request->get('category');
        $image = $request->request->get('image');
        $brandName = $request->request->get('brand'); 

        if (!$name || !$category || !$image || !$brandName) {
            $this->addFlash('error', 'Données invalides.');
            return $this->redirectToRoute('home');
        }

        $existingItem = $clothingItemRepository->findOneBy(['name' => $name, 'image' => $image]);

        if ($existingItem) {
            $clothingItem = $existingItem;
        } else {
            $clothingItem = new ClothingItem();
            $clothingItem->setName($name);
            $clothingItem->setImage($image);
            $clothingItem->setCategory($category);

            $brand = $brandRepository->findOneBy(['name' => $brandName]);

            if (!$brand) {
                $brand = new Partner(); 
                $brand->setName($brandName);
                $entityManager->persist($brand);
            }

            $clothingItem->setBrand($brand);
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

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
    
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
    
            if (!$name || !$email || !$password) {
                $this->addFlash('error', 'Tous les champs sont requis.');
                return $this->redirectToRoute('app_user_new');
            }
    
            $user->setName($name);
            $user->setEmail($email);
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
    
            $user->setRoles(['ROLE_USER']);
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('home');
        }
    
        return $this->render('user/new.html.twig');
    }

}
