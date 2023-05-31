<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        if ($this->getUser()) // Pour le pré-remplissage des champs "Nom" et "Email" si la personne qui envoie le message est un utilisateur déjà enregistré sur notre site
        {
            $contact->setNom($this->getUser()->getNom())
                ->setEmail($this->getUser()->getEmail());
        }
        
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // dd($form->getData());

            $contact = $form->getData();
            // dd($contact);

            $manager->persist($contact);
            $manager->flush();

            // !!!!! EMAIL !!!!!

            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('contact@beerparadise.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getSujet())
            // ->text($contact->getMessage())
            ->htmlTemplate('emails/contact.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'contact' => $contact,
            ]);

            $mailer->send($email);

            // !!!!! Message flash : Message de contact envoyé !!!!!
            $this->addFlash(    // Nécessite un block "for message" dans contactIndex.html.twig pour fonctionner
                'success',      // Nom de l'alerte 
                'Votre message a bien été envoyé à notre équipe'  // Message(s)
            );

            return $this->redirectToRoute('contact.index');
        }

        return $this->render('pages/contact/contactIndex.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
