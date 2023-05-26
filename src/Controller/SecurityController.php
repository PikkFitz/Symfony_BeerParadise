<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login', methods:['GET', 'POST'])]
     /**
     * This controller allows us to login
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response  // AuthenticationUtils nécessaire pour last_username dans login.html.twig
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),  // Récupère l'identifiant de l'utilisateur connecté | Nécessaire dans login.html.twig
            'error' => $authenticationUtils->getLastAuthenticationError(),  // Récupère l'erreur sur la dernière tentative de connexion | Nécessaire dans login.html.twig
        ]);
    }

    
    #[Route('/deconnexion', name: 'security.logout', methods:['GET', 'POST'])]
    /**
     * This controller allows us to logout
     *
     * @return void
     */
    public function logout()
    {
        // Nothing to do here...
    }

    
    #[Route('/inscription', name: 'security.registration', methods:['GET', 'POST'])]
    /**
     * This controller allows us to register
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function registration(Request $request, EntityManagerInterface $manager) : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user = $form->getData();

            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash(
                'success',
                'Le compte utilsateur de M./Mme "'. $user->getNom() .'" a bien été créé !'
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security.login');
        }

        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
