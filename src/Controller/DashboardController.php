<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\OutfitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepository, 
        OutfitRepository $outfitRepository, 
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $totalUsers = $userRepository->count([]);
        $totalOutfits = $outfitRepository->count([]);
        $publicOutfits = $outfitRepository->count(['public' => true]);
    
        $topUsers = $userRepository->createQueryBuilder('u')
            ->leftJoin('u.outfits', 'o')
            ->select('u.name, COUNT(o.id) as outfitCount')
            ->groupBy('u.id')
            ->orderBy('outfitCount', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    
        $topUserNames = array_column($topUsers, 'name');
        $topUserOutfitCounts = array_column($topUsers, 'outfitCount');

        $users = $userRepository->findAll();

        if ($request->isMethod('DELETE')) {
            $userId = $request->request->get('user_id');
            $user = $userRepository->find($userId);

            if ($user) {
                $entityManager->remove($user);
                $entityManager->flush();
                
                return $this->redirectToRoute('app_dashboard');
            }

            return $this->redirectToRoute('app_dashboard');
        }
        
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $userId = $data['id'] ?? null;
            
            $user = $userId ? $userRepository->find($userId) : new User();
            
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
            $user->setRoles(['ROLE_USER']); 

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur créé/modifié avec succès !');
        }

        return $this->render('dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalOutfits' => $totalOutfits,
            'publicOutfits' => $publicOutfits,
            'topUserNames' => $topUserNames,
            'topUserOutfitCounts' => $topUserOutfitCounts,
            'users' => $users,
        ]);
    }
}