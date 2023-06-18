<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\SousCategorie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SousCategorieCrudController extends AbstractCrudController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getEntityFqcn(): string
    {
        return SousCategorie::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options sous-catégorie "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Sous-catégories')
            ->setEntityLabelInSingular('Sous-catégorie')
            ->setPageTitle('index', 'BeerParadise - Administration des sous-catégories')
            ->setPageTitle('new', 'BeerParadise - Ajout d\'une sous-catégorie')
            ->setPageTitle('edit', function (SousCategorie $sousCategorie) 
                {
                    return 'Modification de la sous-catégorie : ' . $sousCategorie->getNom();
                })
            ->setPageTitle('detail', function (SousCategorie $sousCategorie) 
                {
                    return $sousCategorie->getNom();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre de SousCategories par page
    }

    public function configureFields(string $pageName): iterable
    {
        // On recupère toutes les Categories existantes (pour les choix de la Categorie quand on modifie/ajoute une SousCategorie)
        $categories = $this->manager->getRepository(Categorie::class)->findAll();

        if ($pageName=="new") 
        {
            return [
                TextField::new('nom'),
                TextareaField::new('description'),
                ChoiceField::new('categorie')
                    ->setChoices(array_combine($categories, $categories))
                    ->renderExpanded()
                    ->renderAsBadges(),
                TextField::new('imageFile')->setFormType(VichImageType::class),
            ];
        } 
        elseif ($pageName == "edit") 
        {
            return [
                TextField::new('nom'),
                TextareaField::new('description'),
                ChoiceField::new('categorie')
                    ->setChoices(array_combine($categories, $categories))
                    ->renderExpanded()
                    ->renderAsBadges(),
                TextField::new('imageFile')->setFormType(VichImageType::class),
            ];
        } 
        elseif ($pageName == "detail") 
        {
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextField::new('categorie'),
                TextareaField::new('description'),
                ArrayField::new('Produits'),
                ImageField::new('imageName', 'Image')->setBasePath('/images/sousCategorie'),
                DateTimeField::new('createdAt'),
                DateTimeField::new('updatedAt'),
            ];
        } 
        else // page : index
        { 
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextField::new('categorie'),
                ImageField::new('imageName', 'Image')->setBasePath('/images/sousCategorie')
            ];
        }
    }
}


// Exemple pour champs avec choix :

// return [
//     TextField::new('nom'),
//     TextField::new('description'),
//     ChoiceField::new('categorie')
//         ->setChoices(array_combine($categories, $categories))
//         ->renderExpanded()
//         ->renderAsBadges(),
//     AssociationField::new('produits')
//         ->setFormTypeOptions([
//             'multiple' => true,
//             'class' => Produit::class,
//             'choices' => $produits,
//         ]),
// ];
