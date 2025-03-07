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
use App\Entity\Wardrobe;

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

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }

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
            
            if (!$userId) {
                $wardrobe = new Wardrobe();
                $user->setWardrobe($wardrobe);  
            }            
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }

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
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }

        $partner = $partnerRepository->find($id);

        if ($partner) {
            $entityManager->remove($partner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard');
    }


    #[Route('/dashboard/user/{id}/role/{role}', name: 'change_role_user', methods: ['POST'])]
    public function changeRoleUser($id, $role, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }

        $user = $userRepository->find($id);

        if ($user) {
            $user->setRoles([$role]);
            $entityManager->flush();

            $this->addFlash('success', 'Le rôle de l\'utilisateur a été modifié avec succès !');
        } else {
            $this->addFlash('error', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/outfit/{id}/deleteAdmin', name: 'delete_outfit_admin', methods: ['POST'])]
    public function deleteClothingItemAdmin($id, EntityManagerInterface $entityManager, OutfitRepository $outfitRepository): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }
        
        $clothingItem = $outfitRepository->find($id);

        $userId = $clothingItem->getOwner()->getId(); 
        $entityManager->remove($clothingItem);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_show', ['id' => $userId]);
        
    }

}