<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'produit')]
    public function index(ProduitRepository $repository): Response
    {
        $produits = $repository->findBy(['sousCategorie' => $this->getSousCategorie()]);

        return $this->render('pages/produit/produitIndex.html.twig', [
            'produits' => $produits
        ]);
    }
}
