<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategorieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options catégorie "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Catégories')
            ->setEntityLabelInSingular('Catégorie')
            ->setPageTitle('index', 'BeerParadise - Administration des catégories')
            ->setPageTitle('new', 'BeerParadise - Ajout d\'une catégorie')
            ->setPageTitle('edit', function (Categorie $categorie) 
                {
                    return 'Modification de la catégorie : ' . $categorie->getNom();
                })
            ->setPageTitle('detail', function (Categorie $categorie) 
                {
                    return $categorie->getNom();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre de Categories par page
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName=="new") 
        {
            return [
                TextField::new('nom'),
                TextareaField::new('description'),
                TextField::new('imageFile')->setFormType(VichImageType::class),
            ];
        } 
        elseif ($pageName == "edit") 
        {
            return [
                TextField::new('nom'),
                TextareaField::new('description'),
                TextField::new('imageFile')->setFormType(VichImageType::class),
            ];
        } 
        elseif ($pageName == "detail") 
        {
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextareaField::new('description'),
                ArrayField::new('sousCategories'),
                ImageField::new('imageName', 'Image')->setBasePath('/images/categorie')
            ];
        } 
        else // page : index
        { 
            return [
                IdField::new('id'),
                TextField::new('nom'),
                ImageField::new('imageName', 'Image')->setBasePath('/images/categorie')
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