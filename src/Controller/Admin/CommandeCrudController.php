<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options utilisateur "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Commandes')
            ->setEntityLabelInSingular('Commande')
            ->setPageTitle('index', 'BeerParadise - Administration des commandes')
            ->setPageTitle('new', 'Ajout d\'une commande')
            ->setPageTitle('edit', function (Commande $commande) 
                {
                    return 'Modification de la commandes : ' . $commande->getId();
                })
            ->setPageTitle('detail', function (Commande $commande) 
                {
                    return $commande->getId();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre de commandes par page
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName=="new") 
            return [
                yield TextField::new('nom'),
                yield TextField::new('email'),
                $roles = ['ROLE_ADMIN', 'ROLE_USER'],
                yield ChoiceField::new('roles')
                    ->setChoices(array_combine($roles, $roles))
                    ->allowMultipleChoices()
                    ->renderExpanded()
                    ->renderAsBadges(),
                yield TextField::new('plainPassword')
                    ->setFormType(RepeatedType::class)
                    ->setFormTypeOptions([
                        'type' => PasswordType::class,
                        'first_options' => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Confirmation du mot de passe'],
                    ]),      
            ];
        elseif ($pageName=="edit")
            return [
                yield TextField::new('nom'),
                yield TextField::new('email'),
                $roles = ['ROLE_ADMIN', 'ROLE_USER'],
                yield ChoiceField::new('roles')
                    ->setChoices(array_combine($roles, $roles))
                    ->allowMultipleChoices()
                    ->renderExpanded()
                    ->renderAsBadges(),
            ];
        elseif ($pageName=="detail")
            return [
                yield IdField::new('id'),
                yield TextField::new('nom'),
                yield TextField::new('email'),
                yield ArrayField::new('roles'),
                yield ArrayField::new('adresses'),
                yield ArrayField::new('commandes'),
            ];
        else // page : index
            return [
                yield IdField::new('id'),
                yield TextField::new('nom'),
                yield TextField::new('email'),
            ];
    }
}
