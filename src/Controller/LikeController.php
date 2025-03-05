<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Outfit;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/like', name: 'like_')]
class LikeController extends AbstractController
{
    #[Route('/{id}', name: 'toggle_like', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function toggleLike(Outfit $outfit, LikeRepository $likeRepository, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingLike = $likeRepository->findOneBy(['outfit' => $outfit, 'owner' => $user]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();

            return $this->json(['liked' => false, 'likesCount' => $outfit->getLikes()->count()]);
        }

        $like = new Like();
        $like->setOutfit($outfit);
        $like->setOwner($user);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['liked' => true, 'likesCount' => $outfit->getLikes()->count()]);
    }

}
