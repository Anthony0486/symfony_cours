<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController 
{

#[Route('/', name: 'app_home')]
public function index(): Response
    {
         $formations = [
    [
        'title' => 'Développement PHP',
        'description' => 'Découvrir le développement côté serveur.',
    ],
    [
        'title' => 'Symfony',
        'description' => 'Structurer une application web en PHP.',
    ],
    [
        'title' => 'Base de données',
        'description' => 'Apprendre à administrer des bases de données.',
    ],
    [
        'title' => 'Déployer une application Web',
        'description' => 'Apprendre à préparer et éffectuer le déploiement d\'une application web.',
    ]
    
];
     return $this->render('home/index.html.twig', [
        'formations' => $formations
     ]);
    }
}