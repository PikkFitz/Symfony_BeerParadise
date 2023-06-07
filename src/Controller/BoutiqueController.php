<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\SousCategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BoutiqueController extends AbstractController
{
    #[Route('categorie/', name: 'categorie.index')]
    public function categorie(CategorieRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('pages/categorie/categorieIndex.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('categorie/{categorie}/sous-categorie/', name: 'souscategorie.index')]
    public function souscategorie(SousCategorieRepository $sousCategorieRepository, Categorie $categorie): Response
    {
        $sousCategories = $sousCategorieRepository->findBy(['categorie' => $categorie]);

        return $this->render('pages/sousCategorie/sousCategorieIndex.html.twig', [
            'sousCategories' => $sousCategories,
            'categorie' => $categorie,
        ]);
    }


    #[Route('/sous-categorie/{sousCategorie}/produit/', name: 'produit.index')]
    public function produit(ProduitRepository $repository, SousCategorie $sousCategorie): Response
    {
        $produits = $repository->findBy(['sousCategorie' => $sousCategorie]);

        return $this->render('pages/produit/produitIndex.html.twig', [
            'produits' => $produits,
            'sousCategorie' => $sousCategorie,
        ]);
    }


    #[Route('produit/{produit}', name: 'produit.show')]
    public function produitShow(Produit $produit): Response
    {
        return $this->render('pages/produit/produitShow.html.twig', [
            'produit' => $produit,
        ]);
    }
}


?>