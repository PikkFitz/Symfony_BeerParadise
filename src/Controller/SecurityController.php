<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login', methods:['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response  // AuthenticationUtils nécessaire pour last_username dans login.html.twig
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),  // Récupère l'identifiant de l'utilisateur connecté | Nécessaire dans login.html.twig
            'error' => $authenticationUtils->getLastAuthenticationError(),  // Récupère l'erreur sur la dernière tentative de connexion | Nécessaire dans login.html.twig
        ]);
    }

    #[Route('/deconnexion', name: 'security.logout', methods:['GET', 'POST'])]
    public function logout()
    {
        // Nothing to do here...
    }


    #[Route('/inscription', name: 'security.registration', methods:['GET', 'POST'])]
    public function registration() : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
