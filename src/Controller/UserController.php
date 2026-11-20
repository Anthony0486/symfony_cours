<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserService;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService
        ) {}
    #[Route(path:"/users", name:"app_users")]
    public function showAllUsers(): Response{
        $users = $this->userService->getUsers();
        return $this->render("user/show_all_user.html.twig", [
            "users"=> $users
        ]);
    }
    #[Route(path:"/users/{id}", name:"app_user_id")]
    public function showUserById(int $id): Response{
        $user = $this->userService->getUserById($id);
        return $this->render("user/show_user_id.html.twig", [
            "user"=> $user
        ]);
    }
}