<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Outfit;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
final class CommentController extends AbstractController{

    #[Route('/comment/{outfitId}', name: 'comment_create', methods: ['POST'])]
    public function createComment(Request $request, EntityManagerInterface $em, int $outfitId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas valide.');
        }

        $outfit = $em->getRepository(Outfit::class)->find($outfitId);
        if (!$outfit) {
            throw $this->createNotFoundException('Outfit non trouvé.');
        }

        $data = $request->request->all(); 

        if (!empty($data['parent'])) {
            $parentComment = $em->getRepository(Comment::class)->find($data['parent']);
            if ($parentComment) {
                $data['parent'] = $parentComment;
            } else {
                unset($data['parent']); 
            }
        }

        $data['outfit'] = $outfit->getId();
        $data['owner'] = $user->getId();
        $data['createdAt'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'csrf_protection' => false,
        ]);
        
        $form->submit($data, false);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
            return $this->redirectToRoute('public_outfits');
        }

        $this->addFlash('error', 'Erreur lors de l\'ajout du commentaire.');
        return $this->redirectToRoute('public_outfits');
    }
}
