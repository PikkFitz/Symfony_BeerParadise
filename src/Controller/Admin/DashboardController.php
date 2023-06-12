<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Adresse;
use App\Entity\Contact;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\SousCategorie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin.index')]
    #[IsGranted('ROLE_ADMIN')] // Autorise uniquement les personnes ayant le 'ROLE_ADMIN' (administrateurs du site)
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BeerParadise - Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Catégories', 'fa-solid fa-sitemap', Categorie::class);
        yield MenuItem::linkToCrud('Sous-catégories', 'fa-solid fa-folder-tree', SousCategorie::class);
        yield MenuItem::linkToCrud('Produits', 'fa-solid fa-beer-mug-empty', Produit::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Adresses', 'fa-solid fa-location-dot', Adresse::class);
        yield MenuItem::linkToCrud('Demandes de contact', 'fa-solid fa-envelope', Contact::class);
    }
}
