<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\DetailCommande;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande.index')]
    public function commandeIndex(
        SessionInterface $session, 
        UserRepository $userRepository, 
        ProduitRepository $produitRepository, 
        Request $request,
        EntityManagerInterface $manager,
        MailerInterface $mailer): Response
    {
        if (!$this->getUser()) 
        {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(CommandeType::class, null, [
            'user' => $this->getUser()
        ]);

        $user = $userRepository->find($this->getUser());


        // !!!!! IDEM AU PANIER --> A TRANSFORMER EN SERVICE !!!!!

        // On récupère le panier de la session actuelle
        $panier = $session->get("panier", []);  // Soit la session vaut "panier", soit un tableau vide

         // Créer une liste contenant les données du Panier
        $listePanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) 
        {
            $produit = $produitRepository->find($id);

            $listePanier[] = [
                "produit" => $produit,
                "quantite" => $quantite,
            ];

            $total += $produit->getPrix() * $quantite;
        }

        // !!!!!!!!!! !!!!!!!!!! !!!!!!!!!! !!!!!!!!!! !!!!!!!!!!
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // dd($form->getData());
            
            // !!!!! COMMANDE !!!!!
            $commande = new Commande;
            $commande->setUser($user);
            $adresse = $form->getData()['adresses'];
            // dd($adresse->getAdresse());
            $commande->setAdresse($adresse->getAdresse());
            $commande->setCodePostal($adresse->getCodePostal());
            $commande->setVille($adresse->getVille());
            $commande->setPays($adresse->getPays());
            
            foreach ($panier as $id => $quantite) 
            {
                $produit = $produitRepository->find($id); 

                $detailCommande = new DetailCommande;
                $detailCommande->setProduit($produit);
                $detailCommande->setQuantite($quantite);
                // $detailCommande->setPrixTotal($produit->getPrix() * $quantite);
                $detailCommande->setCommande($commande);

                $commande->addDetailCommande($detailCommande);

                $manager->persist($detailCommande);
            }


            $manager->persist($commande);

            $manager->flush();

            // !!!!!!!!!!  PARTIE A DEPLACER ??? DANS LE PAIEMENTCONTROLLER ???  !!!!!!!!!!
            // !!!!! EMAIL !!!!!

            $email = (new TemplatedEmail())
            ->from('contact@beerparadise.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Confirmation de commande' . $commande->getId())
            // ->text($contact->getMessage())
            ->htmlTemplate('emails/commande.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'user' => $user,
                'adresse' => $adresse,
                'commande' => $commande,
                'listePanier' => $listePanier,
                'total' => $total,
            ]);

            $mailer->send($email);

            
            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash(
                'success',
                'Commande validée ! Cheers ! :) P.S : Un e-mail de confirmation vous a été envoyé'
            );

            // On supprime le panier de la session
            // $session->remove('panier');


            // return $this->redirectToRoute('home.index', [
            //     'commande' => $commande,
            // ]);
            // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }


        return $this->render('pages/commande/commandeIndex.html.twig', [
            'form' => $form->createView(),
            'listePanier' => $listePanier,
            'total' => $total,
        ]);
    }


    #[Route('/historique', name: 'historique.index')]
    public function historiqueIndex(CommandeRepository $repository): Response
    {
        $commandes = $repository->findBy(['user' => $this->getUser()]);
        // dd($commandes);

        return $this->render('pages/commande/commandeHistorique.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
