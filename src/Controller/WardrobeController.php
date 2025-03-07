<?php

namespace App\Controller;

use App\Entity\Wardrobe;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\ClothingItemType;
use App\Form\UserType;
use App\Entity\ClothingItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/wardrobe')]
final class WardrobeController extends AbstractController{

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

        $formClothingItem = $this->createForm(ClothingItemType::class, $clothingItem);
        $formClothingItem->handleRequest($request);
        
        $wardrobe = $user->getWardrobe();
        if (!$wardrobe) {
            return $this->render('wardrobe.html.twig', [
                'clothingItems' => [],
                'formClothingItem' => $formClothingItem->createView(),
            ]);
        }

        $clothingItems = $wardrobe->getItems();


    
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

        return $this->render('wardRobe.html.twig', [
            'clothingItems' => $clothingItems,
            'formClothingItem' => $formClothingItem->createView(),
        ]);
    }


    /* décalé car dans le userController marche pas jsp pourquoi */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        
        $user = new User();
        $wardrobe = new Wardrobe();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);
            $user->setWardrobe($wardrobe);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('home');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
