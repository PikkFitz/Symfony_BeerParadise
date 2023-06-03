<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(SessionInterface $session): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la seesion vaut "panier", soit un tableau vide

        return $this->render('pages/panier/panierIndex.html.twig', [

        ]);
    }

    #[Route('/add/{produit}', name: 'add')]
    public function add(Produit $produit, SessionInterface $session): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la seesion vaut "panier", soit un tableau vide

        // On vérifie si le Produit existe déjà dans notre Panier
        // Si oui, alors +1 sur la qantité du Produit
        // Si non, alors on ajoute le Produit au Panier avec la quantité = 1
        if (isset($panier[$produit])) 
        {
            $panier[$produit]++;
        }
        else 
        {
            $panier[$produit]=1;
        }

        // On sauvegarde le Panier dans la session
        $session->set('panier', $panier);


        return $this->render('pages/panier/panierIndex.html.twig', [
            'produit' => $produit,
        ]);
    }
}
