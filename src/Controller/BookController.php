<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    private array $books = [
        ['id' => 1, 'title' => 'Dune', 'author' => 'Frank Herbert'],
        ['id' => 2, 'title' => '1984', 'author' => 'George Orwell'],
        ['id' => 3, 'title' => 'Fondation', 'author' => 'Isaac Asimov'],
    ];
    // private array $books = [];
    #[Route('/livres', name: 'app_books')]
    public function index(): Response
    {

        return $this->render('book/index.html.twig', [
            'books' => $this->books

        ]);
    }

    #[Route('/livres/{id}', name: 'app_books_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $foundBook = null;
        foreach ($this->books as $book) {
            if ($book['id'] === $id) {
                $foundBook = $book;
                break;
            }
        }
        // Si aucun livre ne correspond à l'id, on renvoie une erreur 404
        if ($foundBook === null) {
            throw $this->createNotFoundException('Ce livre n\'existe pas.');
        }
        return $this->render('book/show.html.twig', [
            'book' => $foundBook
        ]);
    }
}