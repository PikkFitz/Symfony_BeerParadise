<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{user}', name: 'user.edit', methods: ['GET', 'POST'])]
    /**
     * This controller allows us to edit user's profil
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function userEdit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if(!$this->getUser())  // On vérifie si l'utilisateur est connecté
        {
            return $this->redirectToRoute('security.login');
        }

        if($this->getUser() !== $user )  // On vérifie que l'utilisateur connecté n'a accès qu'à son propre profil
        {
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword()))
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
            'user' => $user,
        ]);
    }


    #[Route('/utilisateur/edition-mot-de-passe/{user}', name: 'user.editPassword', methods: ['GET', 'POST'])]
    /**
     * This controller allows us to edit user's password
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function userEditPassword(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if(!$this->getUser())  // On vérifie si l'utilisateur est connecté
        {
            return $this->redirectToRoute('security.login');
        }

        if($this->getUser() !== $user )  // On vérifie que l'utilisateur connecté n'a accès qu'à son propre profil
        {
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(UserPasswordType::class);

        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setUpdatedAt(new \DateTimeImmutable()); // Nécessaire de modifier une colonne pour que le preUpdate se déclenche 
                                                               // et que le 'plainPassword' se mette à jour car le 'plainPassword' n'est pas une colonne

                $user->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($user);
                $manager->flush();

                // !!!!! MESSAGE FLASH !!!!!
                $this->addFlash(
                    'success',
                    'Le mot de passe de M./Mme "'. $user->getNom() .'" a bien été modifié !'
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
