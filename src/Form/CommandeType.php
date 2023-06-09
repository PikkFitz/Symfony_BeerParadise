<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('adresses', EntityType::class, [
                'class' => Adresse::class,
                'attr' => [
                    'class' => 'form-control', 
                ],
                'label' => 'Veuillez séléctionner une adresse',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'choices' => 
                    $user->getAdresses(),
                'choice_attr' => function ($choice, $key, $value) {
                    return ['style' => 'color: black;']; // Pour afficher les noms des choix en noir dans le menu déroulant (et non en blanc sur fond blanc...)
                },
                'required' => true,
            ])
            ->add('paiement', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control', 
                ],
                'label' => 'Veuillez sélectionner un moyen de paiement',
                'label_attr' => [
                    'class' => 'form-label mt-3'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'choices' => [
                    'Stripe' => 0,
                    'PayPal' => 1,
                ],
                'choice_attr' => [
                    'PayPal' => ['class' => 'ms-2'],
                    // function ($choice, $key, $value) {
                    //     return ['style' => 'color: black;']; // Pour afficher les noms des choix en noir dans le menu déroulant (et non en blanc sur fond blanc...)
                    // },
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Passer au paiement'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => [],
        ]);
    }
}
