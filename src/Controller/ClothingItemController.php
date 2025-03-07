<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Partner;
use App\Entity\ClothingItem;
use App\Entity\AINotification;
use App\Entity\Wardrobe;
use App\Form\ClothingItemTypeImagePath;
use App\Form\PartnerType;
use App\Repository\PartnerRepository;
use App\Repository\ClothingItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clothing/item')]
final class ClothingItemController extends AbstractController{

    #[Route('/new', name: 'app_clothing_item_new', methods: ['POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        PartnerRepository $partnerRepository
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }
    
        $wardrobe = $user->getWardrobe();
        $data = $request->request->all();
    
        $brand = $partnerRepository->findOneBy(['name' => $data['brand']]);
    
        if (!$brand) {

            $notif = new AINotification();
            $notif->setCreatedAt(new \DateTimeImmutable());
            $notif->setMessage("La marque " . $data['brand'] . " a été ajoutée dans la BDD");
            $notif->setStatus("Pending");

            $entityManager->persist($notif);

            $brand = new Partner();
            $formBrand = $this->createForm(PartnerType::class, $brand, [
                'csrf_protection' => false, 
            ]);
            
            $formBrand->submit(['name' => $data['brand']], false);
    
            if ($formBrand->isSubmitted() && $formBrand->isValid()) {
                $entityManager->persist($brand);
                $entityManager->flush();
            }
        }
    
        $data['brand'] = $brand->getId();
        $clothingItem = new ClothingItem();
    
        $form = $this->createForm(ClothingItemTypeImagePath::class, $clothingItem, [
            'csrf_protection' => false, 
        ]);
    
        $form->submit($data, false); 
    
        if ($form->isSubmitted() && $form->isValid()) {
            $clothingItem->addWardrobe($wardrobe);
            $entityManager->persist($clothingItem);
            $entityManager->flush();
    
            $this->addFlash('success', 'Ajout du vêtement dans la garde-robe');
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }
    
        $this->addFlash('error', 'Erreur lors de l\'ajout du vêtement');
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
    
    

    #[Route('/{id}', name: 'app_clothing_item_delete', methods: ['POST'])]
    public function delete(Request $request, ClothingItem $clothingItem, EntityManagerInterface $entityManager): Response
    {   
        $user = $this->getUser(); 
    
        if (!$user instanceof User) {
            $this->addFlash('error', 'L\'utilisateur n\'est pas valide.');
            return $this->redirectToRoute('home');
        }
    
        $userWardrobe = $user->getWardrobe(); 
        $itemWardrobes = $clothingItem->getWardrobes();
    
        $hasAccess = false;
        foreach ($itemWardrobes as $wardrobe) {
            if ($wardrobe->getId() === $userWardrobe->getId()) {
                $hasAccess = true;
                break;
            }
        }
    
        if (!$hasAccess) {
            $this->addFlash('error', 'Permission Invalide');
            return $this->redirectToRoute('home');
        }
    
        if ($this->isCsrfTokenValid('delete' . $clothingItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($clothingItem);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('wardrobe_list', [], Response::HTTP_SEE_OTHER);
    }
    
}
