<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(SessionInterface $session, ProduitRepository $repository): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

        // On créer une liste contenant les données du Panier
        $listePanier = [];
        
        $total = 0;

        foreach($panier as $id => $quantite) 
        {
            $produit = $repository->find($id);

            $listePanier[] = [
                "produit" => $produit,
                "quantite" => $quantite,
            ];

            $total += $produit->getPrix() * $quantite;
        } 

        return $this->render('pages/panier/panierIndex.html.twig', [
            'listePanier' => $listePanier,
            'total' => $total,
        ]); 
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, ProduitRepository $repository, SessionInterface $session, EntityManagerInterface $manager, RequestStack $requestStack): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

        $id = $request->request->get('id');

        $produit = $repository->findOneBy(['id' => $id]);

        // On récupère la valeur de l'input "quantite"
        $quantite = $request->request->get('quantite', 1);  // 1 : Valeur minimale retournée si null
        
        // On vérifie si le Produit existe déjà dans notre Panier
        if (isset($panier[$id])) 
        {
            // $panier[$id]++;
            $panier[$id] += $quantite;
        }
        else 
        {
            // $panier[$id] = 1;
            $panier[$id] = $quantite;
        }

        // dd($quantite);
        // dd($produit->getStock());

        $stock = $produit->getStock();

        // On vérifie que la quantité de produit est en stock
        if($quantite > $stock)
        {
             // !!!!! MESSAGE FLASH !!!!!
             $this->addFlash(
                'danger',
                'Stock de produit "' . $produit->getNom() . '" insuffisant pour la quantité ('. $quantite . ') demandée...'
            );

            $currentRequest = $requestStack->getCurrentRequest();
            $currentUrl = $currentRequest->getUri();

            // Redirection vers l'URL actuelle
            return new RedirectResponse($currentUrl);
        }
        else
        {
            // On sauvegarde le Panier dans la session
            $session->set('panier', $panier);

            // On enlève du stock la quantité de produit ajoutée au panier 
            $produit->setStock(($produit->getStock()) - $quantite); 

            $manager->persist($produit);
            $manager->flush();

            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash(
                'success',
                'Ajout au panier : '. $quantite . ' x ' . $produit->getNom()
            );
            
            // $referer trouve l'URL de la page précédente
            // Quand on clic sur le bouton d'ajout au panier, on va sur la route '/add/{id}'
            // Puis, avec return $this->redirect($referer); on revient sur la page sur laquelle on était (comme si on ne changeait pas de page)
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
}

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Produit $produit, SessionInterface $session, EntityManagerInterface $manager): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

        $id = $produit->getId();        // dd($panier);

        // On vérifie si le Produit existe déjà dans notre Panier et si la quantité est supérieure à 1
        // Si oui, alors -1 sur la quantité du Produit
        // Si non, si la quantité est inferieure à 1, alors on supprime la ligne du Produit du Panier
        if (isset($panier[$id])) 
        {
            if($panier[$id] > 1)
            {
                $panier[$id]--;
            }  
            else
            {
                unset($panier[$id]);
            }
        }

        // On rajoute au stock 1 produit retiré du panier (car le bouton supprime les produits 1 par 1)
        $produit->setStock(($produit->getStock()) + 1); 

        $manager->persist($produit);
        $manager->flush();

        // On sauvegarde le Panier dans la session
        $session->set('panier', $panier);

        // !!!!! MESSAGE FLASH !!!!!
        $this->addFlash(
            'danger',
            '1x ' . $produit->getNom() . ' supprimée du panier'
        );

        return $this->redirectToroute("panier");
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Produit $produit, SessionInterface $session, EntityManagerInterface $manager): Response
    {
        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

        $id = $produit->getId();

        // On vérifie si le Produit existe déjà dans notre Panier
        if (isset($panier[$id])) 
        {
            // On récupère la quantité de Produit dans le Panier
            $quantite = $panier[$id]; 

            unset($panier[$id]);

            // On rajoute au stock la quantité de produit retirée du panier 
            $produit->setStock(($produit->getStock()) + $quantite); 

            $manager->persist($produit);
            $manager->flush();
        }


        // On sauvegarde le Panier dans la session
        $session->set('panier', $panier);

        // !!!!! MESSAGE FLASH !!!!!
        $this->addFlash(
            'danger',
            'La ligne de produit "' . $produit->getNom() . '" a été supprimée du panier'
        );

        return $this->redirectToroute("panier");
    }

    #[Route('/deleteAll', name: 'deleteAll')]
    public function deleteAll(SessionInterface $session): Response
    {
        // On supprime le panier de la session
        $session->remove('panier');

        return $this->redirectToroute("panier");
    }
}
