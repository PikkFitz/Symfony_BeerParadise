<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)  // Permet d'ajouter la page detail (dans les options de contact "...")
            ->remove(Crud::PAGE_INDEX, Action::EDIT);  // Permet d'enlever la page edit (dans les options contact "...")
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInPlural('Demandes de contact')
            ->setEntityLabelInSingular('Demande de contact')
            ->setPageTitle('index', 'BeerParadise - Administration des demandes de contact')
            ->setPageTitle('detail', function (Contact $contact) 
            {
                return 'Demande de contact de : ' . $contact->getNom();
            })
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(25)  // Nombre d'utilisateurs par page
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig'); // Pour CKEditor (permet d'avoir un editeur plus complet pour les messages)
                                                                           // (dans la page edit (qui est désactivée, à réactiver dans les actions ci-dessus))
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('nom'),
            TextField::new('email'),
            TextField::new('sujet'),
            TextareaField:: new('message')
                ->hideOnIndex()
                ->setFormType(CKEditorType::class),  // Pour CKEditor (permet d'avoir un editeur plus complet pour les messages)
                                                     // (dans la page edit (qui est désactivée, à réactiver dans les actions ci-dessus))
            DateTimeField::new('createdAt'),
        ];
    }
}
