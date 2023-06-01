<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\SousCategorie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategorieCrudController extends AbstractCrudController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Vérifier si l'entité est une instance de Categorie
        if ($entityInstance instanceof Categorie) 
        {
            // Récupérer les SousCategories de la Categorie
            $sousCategories = $entityInstance->getSousCategories();

            // Parcourir les SousCategories
            foreach ($sousCategories as $sousCategorie) 
            {
                // Vérifier si la SousCategorie appartient déjà à une autre Categorie
                $categorieExistante = $sousCategorie->getCategorie();
                if ($categorieExistante !== null && $categorieExistante !== $entityInstance) 
                {
                    // Retirer la SousCategorie de la Categorie existante
                    $categorieExistante->removeSousCategorie($sousCategorie);
                }

                // Affecter la nouvelle Categorie à la SousCategorie
                $sousCategorie->setCategorie($entityInstance);

                // Persistez les changements de la SousCategorie
                $entityManager->persist($sousCategorie);
            }
        }

        // Appeler la méthode parente pour effectuer les opérations par défaut
        parent::updateEntity($entityManager, $entityInstance);
    }
    
    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options utilisateur "...")
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
            ->setPaginatorPageSize(25); // Nombre d'utilisateurs par page
    }

    
    public function configureFields(string $pageName): iterable
    {
        $sousCategories = $this->manager->getRepository(SousCategorie::class)->findAll();

        if ($pageName=="new") 
        {
            return [
                TextField::new('nom'),
                TextField::new('description'),
                AssociationField::new('sousCategories')
                    ->setFormTypeOptions([
                        'multiple' => true,
                        'class' => SousCategorie::class,
                        'choices' => $sousCategories,
                    ]),
            ];
        } 
        elseif ($pageName == "edit") 
        {
            return [
                TextField::new('nom'),
                TextField::new('description'),
                AssociationField::new('sousCategories')
                    ->setFormTypeOptions([
                        'multiple' => true,
                        'class' => SousCategorie::class,
                        'choices' => $sousCategories,
                    ]),
            ];
        } 
        elseif ($pageName == "detail") 
        {
            return [
                IdField::new('id'),
                TextField::new('nom'),
                TextField::new('description'),
                ArrayField::new('sousCategories'),
            ];
        } 
        else // page : index
        { 
            return [
                IdField::new('id'),
                TextField::new('nom'),
            ];
        }
    }
}
