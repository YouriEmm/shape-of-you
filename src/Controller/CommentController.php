<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Outfit;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{

    #[Route('/comment/{outfitId}', name: 'comment_create', methods: ['POST'])]
    public function createComment(Request $request, EntityManagerInterface $em, $outfitId)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $outfit = $em->getRepository(Outfit::class)->find($outfitId);
    
        if (!$outfit) {
            throw $this->createNotFoundException('Outfit non trouvé.');
        }
    
        $content = $request->get('content');
        $parentId = $request->get('parentId');
    
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setOutfit($outfit);
        $comment->setOwner($user);
        $comment->setCreatedAt(new \DateTimeImmutable());
    
        if ($parentId) {
            $parentComment = $em->getRepository(Comment::class)->find($parentId);
            if ($parentComment) {
                $comment->setParent($parentComment);
            }
        }
    
        // Enregistrer le commentaire
        $em->persist($comment);
        $em->flush();
    
        return $this->redirectToRoute('public_outfits');
    }
    

}