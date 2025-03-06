<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Partner;
use App\Repository\UserRepository;
use App\Repository\OutfitRepository;
use App\Repository\PartnerRepository;
use App\Repository\AINotificationRepository;
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
        EntityManagerInterface $entityManager,
        PartnerRepository $partnerRepository,
        AINotificationRepository $aINotificationRepository
    ): Response {
        $totalUsers = $userRepository->count([]);
        $totalOutfits = $outfitRepository->count([]);
        $publicOutfits = $outfitRepository->count(['public' => true]);
    
        $iaNotif = $aINotificationRepository->findAll();
        
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
        $partners = $partnerRepository->findAll();
        
        if ($request->isMethod('POST') && $request->request->has('user_submit')) {
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
            return $this->redirectToRoute('app_dashboard');
        }

        if ($request->isMethod('POST') && $request->request->has('partner_submit')) {
            $data = $request->request->all();

            $partnerId = $data['partner_id'] ?? null;
            $partner = $partnerId ? $partnerRepository->find($partnerId) : new Partner();
            
            $partner->setName($data['partner_name']);
            $partner->setWebsite($data['partner_url']);

            $entityManager->persist($partner);
            $entityManager->flush();

            $this->addFlash('success', 'Partenaire créé/modifié avec succès !');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalOutfits' => $totalOutfits,
            'publicOutfits' => $publicOutfits,
            'topUserNames' => $topUserNames,
            'topUserOutfitCounts' => $topUserOutfitCounts,
            'partners' => $partners,
            'users' => $users,
            'iaNotifs' => $iaNotif
        ]);
    }

    #[Route('/dashboard/user/{id}/delete', name: 'delete_user_admin', methods: ['POST'])]
    public function deleteClothingItem($id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard/partner/{id}/delete', name: 'delete_partner_admin', methods: ['POST'])]
    public function deletePartner($id, EntityManagerInterface $entityManager, PartnerRepository $partnerRepository)
    {
        $partner = $partnerRepository->find($id);

        if ($partner) {
            $entityManager->remove($partner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard');
    }

}