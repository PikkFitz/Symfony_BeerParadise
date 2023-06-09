<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Adresse;
use App\Entity\Contact;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        // !!!!!  USERS  !!!!

        $users = [];

        $admin = new User();  // On ajoute un administrateur
        $admin->setNom('Administrateur BeerParadise')
            ->setEmail('admin@beerparadise.com')
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
            ->setPlainPassword('password');

        $users[] = $admin;

        $manager->persist($admin);


        for ($l=1; $l <= 30; $l++)  // On ajoute 30 utilisateurs
        { 
            $user = new User();
            $user->setNom($this->faker->name);
            $user->setEmail($this->faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPlainPassword('password');

            $users[] = $user;

            $manager->persist($user);
        }

        //  !!!!! ADRESSES  !!!!!

        $adresses = [];

        for ($i=1; $i <= 40; $i++)  // On ajoute 40 adresses
        { 
            $adresse = new Adresse();
            $adresse->setNom($this->faker->word(1, true));  // 1 --> Nombre de mot(s) générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
            $adresse->setAdresse($this->faker->streetAddress());
            $adresse->setVille($this->faker->city());
            $adresse->setCodePostal($this->faker->postcode());
            $adresse->setPays($this->faker->country());
            
            if($i<=count($users))
            {
                $adresse->setUser($users[$i-1]);
            }
            else
            {
                $adresse->setUser($users[mt_rand(0, count($users)-1)]);
            }

            $adresses[] = $adresse;

            $manager->persist($adresse);
        }

        //  !!!!!  CATEGORIES  !!!!!

        $categories = [];

        for ($i=1; $i <= 5; $i++)  // On génère 5 catégories
        { 
            $categorie = new Categorie();
            if ($i==1) {$categorie->setNom('Blanches');} if ($i==2) {$categorie->setNom('Blondes');} if ($i==3) {$categorie->setNom('Ambrées');} if ($i==4) {$categorie->setNom('Brunes et noires');} if ($i==5) {$categorie->setNom('Spéciales');}
            // $categorie->setNom('Categorie' . $i);
            $categorie->setDescription($this->faker->text(200)); // 200 --> Nombre de caractères générés


            // for ($j=0; $j < mt_rand(5, 10); $j++)  // On ajoute entre 5 et 10 sous-catégories par catégorie
            //     { 
            //         $categorie->addSousCategory($sousCategories[mt_rand(0, count($sousCategories)-1)]);  
            //         // Pour la catégorie en cours, on ajoute une sous-catgorie au hasard
            //     }
                
            $categories[] = $categorie;

            $manager->persist($categorie);
        }


        //  !!!!!  SOUS-CATEGORIES  !!!!!

        $sousCategories = [];

        for ($i=1; $i <= 50; $i++)  // On génère 50 sous-catégories
        { 
            $sousCategorie = new SousCategorie();
            $sousCategorie->setNom('sousCategorie' . $i);
            $sousCategorie->setDescription($this->faker->text(200)); // 200 --> Nombre de caractères générés
            $sousCategorie->setCategorie($categories[mt_rand(0, count($categories)-1)]);

            // for ($j=0; $j < mt_rand(5, 15); $j++)  // On ajoute entre 5 et 15 produits par sous-catégorie
            //     { 
            //         $sousCategorie->addProduit($produits[mt_rand(0, count($produits)-1)]);
            //         // Pour la sous-catégorie en cours, on ajoute un produit au hasard
            //     }
                
            $sousCategories[] = $sousCategorie;

            $manager->persist($sousCategorie);
        }


        //  !!!!!  PRODUITS  !!!!!

        $produits = [];

        for ($i=1; $i <= 200; $i++)  // On génère 200 produits
        { 
            $produit = new Produit();
            $produit->setNom($this->faker->word(1, true))  // 1 --> Nombre de mot(s) générés | true --> Retourne un 'string' au lieu d'un 'array' (tableau)
                    ->setDescription($this->faker->text(200)) // 200 --> Nombre de mots générés
                    ->setPrix(mt_rand(2, 10))
                    ->setStock(mt_rand(1, 10000))
                    ->setSousCategorie($sousCategories[mt_rand(0, count($sousCategories)-1)]);
                
            $produits[] = $produit;

            $manager->persist($produit);
        }


        //  !!!!!  CONTACTS  !!!!!

        $contacts = [];

        for ($i=1; $i <= 10; $i++)  // On génère 10 demandes de contact
        { 
            $contact = new Contact();
            $contact->setNom($this->faker->name)
                ->setEmail($this->faker->email())
                ->setSujet('Demande n°' . ($i))
                ->setMessage($this->faker->text());
       
            $contacts[] = $contact;

            $manager->persist($contact);
        }

        $manager->flush();
    }
}
