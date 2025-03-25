<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishController extends AbstractController
{

    #[Route('/wishes',name:'app_wish_list')]
    public function list(): Response
    {
        return $this->render('wish/list.html.twig',
            ['wishes' => ['Learn Symfony', 'Learn Docker', 'Learn API Platform', 'Master JavaScript', 'Master React']]);
    }

    #[Route('/wishes/{id}',name:'app_wish_detail',requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        return $this->render('wish/detail.html.twig', ['id' => $id]);
    }
}
