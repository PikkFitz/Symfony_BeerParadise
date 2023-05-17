<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager): void
    {
        //  !!!!! PRODUITS  !!!!!

        $produits = [];

        for ($i=1; $i <= 200; $i++)  // On génère 200 produits
        { 
            $produit = new Produit();
            $produit->setNom($this->faker->word(1, true))  // 1 --> Nombre de mot(s) générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
                    ->setDescription($this->faker->text(200)) // 200 --> Nombre de mots générés
                    ->setPrix(mt_rand(1, 1000))
                    ->setStock(mt_rand(1, 10000));
            
                
            $produits[] = $produit;

            $manager->persist($produit);
        }

        //  !!!!! SOUS-CATEGORIES  !!!!!

        $sousCategories = [];

        for ($i=1; $i <= 50; $i++)  // On génère 50 sous-catégories
        { 
            $sousCategorie = new SousCategorie();
            $sousCategorie->setNom('sousCategorie' . $i);

            for ($j=0; $j < mt_rand(5, 15); $j++)  // On ajoute entre 5 et 15 produits par sous-catégorie
                { 
                    $sousCategorie->addProduit($produits[mt_rand(0, count($produits)-1)]);
                    // Pour la sous-catégorie en cours, on ajoute un produit au hasard
                }
                
            $sousCategories[] = $sousCategorie;

            $manager->persist($sousCategorie);
        }

        //  !!!!! CATEGORIES  !!!!!

        $categories = [];

        for ($i=1; $i <= 5; $i++)  // On génère 5 catégories
        { 
            $categorie = new Categorie();
            $categorie->setNom('Categorie' . $i);

            for ($j=0; $j < mt_rand(5, 10); $j++)  // On ajoute entre 5 et 10 sous-catégories par catégorie
                { 
                    $categorie->addSousCategory($sousCategories[mt_rand(0, count($sousCategories)-1)]);  
                    // Pour la catégorie en cours, on ajoute une sous-catgorie au hasard
                }
                
            $categories[] = $categorie;

            $manager->persist($categorie);
        }

        $manager->flush();
    }
}
