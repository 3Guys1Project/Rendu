<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MaintenanceController extends AbstractController
{
    #[Route('/maintenance', name: 'maintenance')]
    public function maintenance(): Response
    {
        return $this->render('maintenance.html.twig');
    }
}
