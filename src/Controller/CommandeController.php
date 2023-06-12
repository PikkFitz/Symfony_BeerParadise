<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande.index')]
    public function commandeIndex(): Response
    {
        if (!$this->getUser()) 
        {
            return $this->redirectToRoute('security.login');
        }

        $form

        return $this->render('pages/commande/commandeIndex.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
}
