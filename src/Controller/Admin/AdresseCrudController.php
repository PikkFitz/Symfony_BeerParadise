<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Adresse;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdresseCrudController extends AbstractCrudController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public static function getEntityFqcn(): string
    {
        return Adresse::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options d'adresse "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Adresses')
            ->setEntityLabelInSingular('Adresse')
            ->setPageTitle('index', 'BeerParadise - Administration des adresses')
            ->setPageTitle('new', 'Ajout d\'une adresse')
            ->setPageTitle('edit', function (Adresse $adresse) 
                {
                    return 'Modification de l\'adresse : ' . $adresse->getNom();
                })
            ->setPageTitle('detail', function (Adresse $adresse) 
                {
                    return $adresse->getNom();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre d'adresses par page
    }

    public function configureFields(string $pageName): iterable
    {
        // On recupère tous les Users existants (pour les choix de l'utiliasteur quand on modifie/ajoute une Adresse)
        $users = $this->manager->getRepository(User::class)->findAll();

        if ($pageName=="new") 
            return [
                yield TextField::new('nom'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield IntegerField::new('codePostal'),
                yield TextField::new('pays'),
                yield AssociationField::new('user')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => User::class,
                        'choices' => $users,
                    ]),
            ];
        elseif ($pageName=="edit")
            return [
                yield TextField::new('nom'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield IntegerField::new('codePostal'),
                yield TextField::new('pays'),
                yield AssociationField::new('user')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => User::class,
                        'choices' => $users,
                    ]),
            ];
        elseif ($pageName=="detail")
            return [
                yield IdField::new('id'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield IntegerField::new('codePostal'),
                yield TextField::new('pays'),
                yield IntegerField::new('user'),
            ];
        else // page : index
            return [
                yield IdField::new('id'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield IntegerField::new('codePostal'),
                yield TextField::new('pays'),
                yield IntegerField::new('user'),
            ];
    }
}
