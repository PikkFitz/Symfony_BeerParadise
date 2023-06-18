<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdresseController extends AbstractController
{
    #[Route('adresse/', name: 'adresse.index')]
    #[Security("is_granted('ROLE_USER') and user === adresse.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page des adresses 
    // ET SEULEMENT l'utilisateur à qui "appartiennent" ces adresses
    public function adresse(AdresseRepository $repository): Response
    {
        $adresses = $repository->findBy(['user' => $this->getUser()]);

        // dd($adresses);

        return $this->render('pages/adresse/adresseIndex.html.twig', [
            'adresses' => $adresses,
        ]);
    }


    #[Route('adresse/ajout', 'adresse.new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === adresse.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page d'ajout des adresses 
    // ET SEULEMENT l'utilisateur à qui "appartiennent" ces adresses
    /**
     * This function show a form to add an address
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $adresse = new Adresse();

        $form = $this->createForm(AdresseType::class, $adresse);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $adresse = $form->getData();
            $adresse->setUser($this->getUser());
            // dd($adresse);

            $manager->persist($adresse);
            $manager->flush();

            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash(
                'success',
                'L\'adresse  "' . $adresse->getNom() . '" a bien été ajoutée !'
            );

            return $this->redirectToRoute('adresse.index');
        }

        return $this->render('pages/adresse/adresseNew.html.twig',[
            'form' => $form->createView()
        ]);
    }


    #[Route('adresse/modification/{id}', 'adresse.edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_USER') and user === adresse.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de modification des adresses 
    // ET SEULEMENT l'utilisateur à qui "appartiennent" ces adresses
    /**
     * This function show a form to edit an address when click on the "Modifier" button
     *
     * @param Adresse $adresse
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Adresse $adresse, Request $request, EntityManagerInterface $manager): Response
    {
        // On récupère l'adresse (en paramètre de la fonction edit()), afin de récupérer son id)

        $form = $this->createForm(AdresseType::class, $adresse);
        $form-> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $adresse = $form->getData();
            // dd($adresse);

            $manager->persist($adresse);
            $manager->flush();

            // !!!!! MESSAGE FLASH !!!!!
            $this->addFlash(
                'success',
                'L\'adresse  "' . $adresse->getNom() . '" a bien été modifiée !'
            );

            return $this->redirectToRoute('adresse.index');
        }

        return $this->render('pages/adresse/adresseEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('adresse/suppression/{id}', 'adresse.delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER') and user === adresse.getUser()")]
    // Autorise uniquement les personnes ayant le 'ROLE_USER' (utilisateurs connectés) à accéder à la page de suppresion d'une adresse 
    // ET SEULEMENT l'utilisateur à qui "appartient" cette adresse
    /**
     * This function delete the selected address when click on the "Supprimer" button
     *
     * @param Adresse $adresse
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Adresse $adresse, EntityManagerInterface $manager): Response
    {
        $manager->remove($adresse);
        $manager->flush();

        // !!!!! MESSAGE FLASH !!!!!
        $this->addFlash(
            'danger',
            'L\'adresse  "' . $adresse->getNom() . '" a bien été supprimée !'
        );
        
        return $this->redirectToRoute('adresse.index');
    }


    // /!\ Pour les Adresses de Commandes... En TEST... Non fonctionnel /!\
    #[Route('user_adresses/{id}', name: 'user_adresses')]
    public function userAdresses($id, Request $request, AdresseRepository $adresseRepository): JsonResponse
    {
        // dd($userId);

        $adresses = $adresseRepository->findBy(['user' => $id]);

        $result = [];

        foreach ($adresses as $a) {
            $result[] = [
                "id" => $a->getId(),
                "nom" => $a->getNom(),
                "adresse" => $a->getAdresse(),
                "ville" => $a->getVille()
                
            ];
        }

        // dd($adresses);

        // On renvoie les adresses en tant que réponse JSON
        return new JsonResponse($result);
    }


}