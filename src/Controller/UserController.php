<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    #[Route('/profil/{name}', name: 'app_profile')]
    public function profile(string $name): Response
    {
        $age = 40;
        $city = 'Toulouse';
        
        return $this->render('user/profile.html.twig', [
            'name' => $name,
            'age' => $age,
            'city'=> $city
        ]);
    }
}