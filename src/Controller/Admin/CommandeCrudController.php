<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Adresse;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommandeCrudController extends AbstractCrudController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options des commandes "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Commandes')
            ->setEntityLabelInSingular('Commande')
            ->setPageTitle('index', 'BeerParadise - Administration des commandes')
            ->setPageTitle('new', 'Ajout d\'une commande')
            ->setPageTitle('edit', function (Commande $commande) 
                {
                    return 'Modification de la commande n°' . $commande->getId();
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
        // On recupère tous les Users et Adresses existantes 
        $users = $this->manager->getRepository(User::class)->findAll();
        $adresses = $this->manager->getRepository(Adresse::class)->findAll();
        
        if ($pageName=="new") 
            return [
                yield AssociationField::new('user')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => User::class,
                        'choices' => $users,
                    ]),
                    yield TextField::new('adresse'),
                    yield TextField::new('ville'),
                    yield TextField::new('codePostal'),
                    yield TextField::new('pays'),
            ];
        elseif ($pageName=="edit")
            return [
                yield AssociationField::new('user')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => User::class,
                        'choices' => $users,
                    ]),
                    yield TextField::new('adresse'),
                    yield TextField::new('ville'),
                    yield TextField::new('codePostal'),
                    yield TextField::new('pays'),
            ];
        elseif ($pageName=="detail")
            return [
                yield IdField::new('id'),
                yield TextField::new('user'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield TextField::new('codePostal'),
                yield TextField::new('pays'),
                yield ArrayField::new('detailCommandes'),
                yield DateTimeField::new('createdAt'),
                yield DateTimeField::new('updatedAt'),
            ];
        else // page : index
            return [
                yield IdField::new('id'),
                yield TextField::new('user'),
                yield TextField::new('adresse'),
                yield TextField::new('ville'),
                yield TextField::new('codePostal'),
                yield TextField::new('pays'),
            ];
    }
}
