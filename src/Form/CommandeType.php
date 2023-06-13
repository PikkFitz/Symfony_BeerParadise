<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

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
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'choices' => 
                    $user->getAdresses(),
                'choice_attr' => function ($choice, $key, $value) {
                    return ['style' => 'color: black;']; // Pour afficher les noms des choix en noir (et non en blanc sur fond blanc...)
                },
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
