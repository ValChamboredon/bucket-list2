<?php
// src/Controller/WishController.php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Wish;
use App\Form\CommentType;
use App\Form\WishType;
use App\Repository\CommentRepository;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;


final class WishController extends AbstractController
{
    #[Route('/wishes', name: 'app_wish_list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
        ]);
    }

    #[Route('/wishes/{id}', name: 'app_wish_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, WishRepository $wishRepository, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
        }

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $user = $security->getUser();
            if ($user) {
                $comment->setAuthor($user);
                $comment->setWish($wish);
                $comment->setCreatedAt(new \DateTimeImmutable());

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Commentaire ajouté avec succès !');
                return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
            } else {
                $this->addFlash('danger', 'Utilisateur non connecté.');
            }
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/wishes/create', name: 'wish_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setIsPublished(true);
            $wish->setDateCreated(new \DateTime());
            $wish->setUser($this->getUser());

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'Idea successfully added!');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }

    #[Route('/wishes/{id}/update', name: 'wish_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(int $id, WishRepository $wishRepository, Request $request, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
        }
        if ($wish->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }


        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setDateUpdated(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Idea successfully updated!');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }

    #[Route('/wishes/{id}/delete', name: 'wish_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id, WishRepository $wishRepository, Request $request, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
        }
        if (!($wish->getUser() === $this->getUser() || $this->isGranted('ROLE_ADMIN'))) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $id, $request->query->get('token'))) {
            $em->remove($wish);
            $em->flush();
            $this->addFlash('success', 'This wish has been deleted.');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_wish_list');
    }


    #[Route('/comment/{id}/delete', name: 'comment_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteComment(int $id, CommentRepository $commentRepository, EntityManagerInterface $em, Security $security): Response
    {
        $comment = $commentRepository->find($id);
        $user = $security->getUser();

        if ($comment && $user && $comment->getWish()->getUser() === $user) {
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès !');
        } else {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        return $this->redirectToRoute('app_wish_detail', ['id' => $comment->getWish()->getId()]);
    }

    #[Route('/comment/{id}/edit', name: 'comment_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editComment(int $id, CommentRepository $commentRepository, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $comment = $commentRepository->find($id);
        $user = $security->getUser();

        if (!$comment || !$user || $comment->getWish()->getUser() !== $user) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier ce commentaire.');
            return $this->redirectToRoute('app_wish_detail', ['id' => $comment->getWish()->getId()]);
        }

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Commentaire modifié avec succès !');
            return $this->redirectToRoute('app_wish_detail', ['id' => $comment->getWish()->getId()]);
        }

        return $this->render('wish/edit_comment.html.twig', [
            'commentForm' => $commentForm->createView(),
            'comment' => $comment,
        ]);
    }


}