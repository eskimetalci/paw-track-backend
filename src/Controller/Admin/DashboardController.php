<?php

namespace App\Controller\Admin;

use App\Entity\Animal;
use App\Entity\BlogPost;
use App\Entity\MedicineLog;
use App\Entity\PooLog;
use App\Entity\User;
use App\Entity\VaccineLog;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('🐾 PawTrack Admin')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class);
        
        yield MenuItem::section('Animals');
        yield MenuItem::linkToCrud('Animals', 'fa fa-paw', Animal::class);
        
        yield MenuItem::section('Health Logs');
        yield MenuItem::linkToCrud('Poo Logs', 'fa fa-toilet', PooLog::class);
        yield MenuItem::linkToCrud('Medicine Logs', 'fa fa-pills', MedicineLog::class);
        yield MenuItem::linkToCrud('Vaccine Logs', 'fa fa-syringe', VaccineLog::class);
        
        yield MenuItem::section('Content');
        yield MenuItem::linkToCrud('Blog Posts', 'fa fa-newspaper', BlogPost::class);
        
        yield MenuItem::section('');
        yield MenuItem::linkToUrl('API Docs', 'fa fa-book', '/api/docs');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
