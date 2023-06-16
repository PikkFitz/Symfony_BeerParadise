<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DetailCommandeCrudController extends AbstractCrudController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getEntityFqcn(): string
    {
        return DetailCommande::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);  // Permet d'ajouter la page detail (dans les options des details de commande "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Details Commande')
            ->setEntityLabelInSingular('Detail Commande')
            ->setPageTitle('index', 'BeerParadise - Administration des details de commande')
            ->setPageTitle('new', 'Ajout d\'un detail de commande')
            ->setPageTitle('edit', function (DetailCommande $detailCommande) 
                {
                    return 'Modification du detail de commande n°' . $detailCommande->getId();
                })
            ->setPageTitle('detail', function (DetailCommande $detailCommande) 
                {
                    return $detailCommande->getId();
                })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25); // Nombre de commandes par page
    }

    public function configureFields(string $pageName): iterable
    {
        // On recupère tous les Produits et Commandes existantes
        $produits = $this->manager->getRepository(Produit::class)->findAll();
        $commandes = $this->manager->getRepository(Commande::class)->findAll();
        
        if ($pageName=="new") 
            return [
                yield AssociationField::new('produit')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => Produit::class,
                        'choices' => $produits,
                    ]),
                yield IntegerField::new('quantite'),
                yield AssociationField::new('commande')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => Commande::class,
                        'choices' => $commandes,
                    ]),
            ];
        elseif ($pageName=="edit")
            return [
                yield AssociationField::new('produit')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => Produit::class,
                        'choices' => $produits,
                    ]),
                yield IntegerField::new('quantite'),
                yield AssociationField::new('commande')
                    ->setFormTypeOptions([
                        'multiple' => false,
                        'class' => Commande::class,
                        'choices' => $commandes,
                    ]),
            ];
        elseif ($pageName=="detail")
            return [
                yield IdField::new('id'),
                yield TextField::new('produit'),
                yield IntegerField::new('quantite'),
                yield NumberField::new('prixTotal'),
                yield IntegerField::new('commande'),
            ];
        else // page : index
            return [
                yield IdField::new('id'),
                yield TextField::new('produit'),
                yield IntegerField::new('quantite'),
                yield NumberField::new('prixTotal'),
                yield IntegerField::new('commande'),
            ];
    }
}
