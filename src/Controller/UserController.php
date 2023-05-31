<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{choosenUser}', name: 'user.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') and user === choosenUser")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification du profil 
    // ET SEULEMENT l'utilisateur à qui "appartient" ce profil
    /**
     * This controller allows us to edit user's profil
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function userEdit(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        // if(!$this->getUser())  // On vérifie si l'utilisateur est connecté
        // {
        //     return $this->redirectToRoute('security.login');
        // }

        // if($this->getUser() !== $choosenUser )  // On vérifie que l'utilisateur connecté n'a accès qu'à son propre profil
        // {
        //     return $this->redirectToRoute('home.index');
        // }

        $form = $this->createForm(UserType::class, $choosenUser);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()->getPlainPassword()))
            {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();

                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash(
                    'success',
                    'Le compte utilsateur de M./Mme "'. $user->getNom() .'" a bien été modifié !'
                );

                return $this->redirectToRoute('home.index'); 
            }
            else
            {
                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash(
                    'danger',
                    'Le mot de passe renseigné est incorrect !'
                );
            }
        }

        return $this->render('pages/user/userEdit.html.twig', [
            'form' => $form->createView(),
            'user' => $choosenUser,
        ]);
    }


    #[Route('/utilisateur/edition-mot-de-passe/{choosenUser}', name: 'user.editPassword', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification du profil 
    // ET SEULEMENT l'utilisateur à qui "appartient" ce mot de passe
    /**
     * This controller allows us to edit user's password
     *
     * @param User $choosenUser
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function userEditPassword(User $choosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        // if(!$this->getUser())  // On vérifie si l'utilisateur est connecté
        // {
        //     return $this->redirectToRoute('security.login');
        // }

        // if($this->getUser() !== $choosenUser )  // On vérifie que l'utilisateur connecté n'a accès qu'à son propre profil
        // {
        //     return $this->redirectToRoute('home.index');
        // }

        $form = $this->createForm(UserPasswordType::class);

        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($choosenUser, $form->getData()['plainPassword']))
            {
                $choosenUser->setUpdatedAt(new \DateTimeImmutable()); // Nécessaire de modifier une colonne pour que le preUpdate se déclenche 
                                                               // et que le 'plainPassword' se mette à jour car le 'plainPassword' n'est pas une colonne

                $choosenUser->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($choosenUser);
                $manager->flush();

                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash(
                    'success',
                    'Le mot de passe de M./Mme "'. $choosenUser->getNom() .'" a bien été modifié !'
                );

                return $this->redirectToRoute('home.index');
            }
            else
            {
                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash(
                    'danger',
                    'Le mot de passe renseigné est incorrect!'
                );
            }
        }

        return $this->render('pages/user/userEditPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
