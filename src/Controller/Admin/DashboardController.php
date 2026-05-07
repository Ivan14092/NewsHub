<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_article_index');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('NewsHub Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Статті', 'fa fa-newspaper', 'admin_article_index');
        yield MenuItem::linkToRoute('Категорії', 'fa fa-folder', 'admin_category_index');
        yield MenuItem::linkToRoute('Користувачі', 'fa fa-users', 'admin_user_index');
        yield MenuItem::linkToRoute('На сайт', 'fa fa-arrow-left', 'app_home');
    }
}