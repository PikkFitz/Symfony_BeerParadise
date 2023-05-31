<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options utilisateur "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Utilisateurs')
            ->setEntityLabelInSingular('Utilisateur')
            ->setPageTitle('index', 'BeerParadise - Administration utilisateurs')
            ->setPageTitle('new', 'BeerParadise - Ajout d\'un utilisateur')
            ->setPageTitle('edit', 'BeerParadise - Modification d\'un utilisateur')
            ->setPageTitle('detail', 'BeerParadise - Details utilisateur')
            ->setPaginatorPageSize(25); // Nombre d'utilisateurs par page
    }

    
    public function configureFields(string $pageName): iterable
    {
        if ($pageName=="new") 
            return [
                TextField::new('nom'),
                TextField::new('email'),
                ArrayField::new('roles'),
                TextField::new('plainPassword')
                    ->setFormType(RepeatedType::class)
                    ->setFormTypeOptions([
                        'type' => PasswordType::class,
                        'first_options' => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Confirmation du mot de passe'],
                    ]),      
            ];
        elseif ($pageName=="edit")
            return [
                TextField::new('nom'),
                TextField::new('email'),
                ArrayField::new('roles'),
            ];
        elseif ($pageName=="detail")
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextField::new('email'),
                ArrayField::new('roles'),
            ];
            else // page : index
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextField::new('email'),
            ];
    }
    
}
