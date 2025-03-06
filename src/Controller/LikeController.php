<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Outfit;
use App\Entity\User;
use App\Form\LikeType;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/like', name: 'like_')]
class LikeController extends AbstractController
{
    #[Route('/{id}', name: 'toggle_like', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleLike(Request $request, Outfit $outfit, LikeRepository $likeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }

        $existingLike = $likeRepository->findOneBy(['outfit' => $outfit, 'owner' => $user]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();

            return $this->json(['liked' => false, 'likesCount' => $outfit->getLikes()->count()]);
        }

        $data = ['outfit' => $outfit->getId(), 'owner' => $user->getId()];
        $like = new Like();
        
        $form = $this->createForm(LikeType::class, $like, [
            'csrf_protection' => false,
        ]);

        $form->submit($data, false); 

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($like);
            $entityManager->flush();

            return $this->json(['liked' => true, 'likesCount' => $outfit->getLikes()->count()]);
        }

        return $this->json(['error' => 'Erreur lors de l\'ajout du like'], Response::HTTP_BAD_REQUEST);
    }
}
