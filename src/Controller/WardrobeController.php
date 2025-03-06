<?php

namespace App\Controller;

use App\Entity\ClothingItem;
use App\Entity\User;
use App\Entity\Wardrobe;
use App\Entity\AINotification;
use App\Repository\ClothingItemRepository;
use App\Repository\PartnerRepository;
use App\Entity\Partner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ClothingItemType; 
use Symfony\Component\HttpFoundation\File\UploadedFile;


class WardrobeController extends AbstractController
{

    #[Route('/wardrobe', name: 'wardrobe_list')]
    public function viewWardrobe( EntityManagerInterface $entityManager, Request $request,): Response
    {
        $user = $this->getUser();
        $clothingItem = new ClothingItem();

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

        $formClothingItem = $this->createForm(ClothingItemType::class, $clothingItem);
        $formClothingItem->handleRequest($request);
    
        if ($formClothingItem->isSubmitted() && $formClothingItem->isValid()) {

            $file = $formClothingItem->get('image')->getData();
            
            if (!$file instanceof UploadedFile) {  
                $this->addFlash('error', 'Aucune image fournie');
                return $this->redirectToRoute('wardrobe_list');    
            }
        
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }
        
            $fileName = uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($uploadDirectory, $fileName);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload');
                return $this->redirectToRoute('wardrobe_list');        
            }
        
            $clothingItem->setImage('/uploads/' . $fileName);
            $clothingItem->addWardrobe($wardrobe);

            $entityManager->persist($clothingItem);
            $entityManager->flush();

            $this->addFlash('success', 'Vêtement ajouté à votre garde-robe !');
            return $this->redirectToRoute('wardrobe_list');
        }

        return $this->render('wardrobe.html.twig', [
            'clothingItems' => $clothingItems,
            'formClothingItem' => $formClothingItem->createView(),
        ]);
    }


    /* décalé car dans le userController marche pas jsp pourquoi */
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
