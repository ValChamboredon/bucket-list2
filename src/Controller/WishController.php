<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

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
    public function detail(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
        }

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish,
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
            $wish->setDateCreated(new \DateTimeImmutable());

            $em->persist($wish);
            $em->flush();

            $this->addFlash('success', 'Idea successfully added!');
            return $this->redirectToRoute('app_wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm,
            'wish' => $wish
        ]);
    }

    #[Route('/wishes/{id}/update', name: 'wish_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(int $id, WishRepository $wishRepository, Request $request, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
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
            'wishForm' => $wishForm,
            'wish' => $wish
        ]);
    }

    #[Route('/wishes/{id}/delete', name: 'wish_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id, WishRepository $wishRepository, Request $request, EntityManagerInterface $em): Response
    {
        $wish = $wishRepository->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('This wish does not exist.');
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
}
