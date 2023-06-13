<?php

namespace App\Controller;

use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande.index')]
    public function commandeIndex(SessionInterface $session, ProduitRepository $repository): Response
    {
        if (!$this->getUser()) 
        {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(CommandeType::class, null, [
            'user' => $this->getUser()
        ]);

        // !!!!! IDEM AU PANIER --> A TRANSFORMER EN SERVICE !!!!!
        
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

         // Créer une liste contenant les données du Panier
        $listePanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) 
        {
            $produit = $repository->find($id);

            $listePanier[] = [
                "produit" => $produit,
                "quantite" => $quantite,
            ];

            $total += $produit->getPrix() * $quantite;
        }

        // !!!!!!!!!! !!!!!!!!!! !!!!!!!!!! !!!!!!!!!! !!!!!!!!!!


        return $this->render('pages/commande/commandeIndex.html.twig', [
            'form' => $form->createView(),
            'listePanier' => $listePanier,
            'total' => $total,
        ]);
    }
}
