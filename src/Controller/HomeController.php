<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index')]
    public function index(ProduitRepository $repository): Response
    {
        // On chercher les 10 derniers produits ajouter à la BDD
        $produits = $repository->findBy([], ['createdAt' => 'DESC'], 10); 


        return $this->render('pages/home.html.twig', [
            'produits' => $produits,
        ]);
    }
}
