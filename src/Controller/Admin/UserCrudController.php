<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->setPageTitle('index', 'BeerParadise - Administration des utilisateurs')
            ->setPageTitle('new', 'Ajout d\'un utilisateur')
            ->setPageTitle('edit', function (User $user) 
                {
                    return 'Modification de l\'utilisateur : ' . $user->getNom();
                })
            ->setPageTitle('detail', function (User $user) 
                {
                    return $user->getNom();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre d'utilisateurs par page
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
            ];
        else // page : index
            return [
                yield IdField::new('id'),
                yield TextField::new('nom'),
                yield TextField::new('email'),
            ];
    }
    
}
