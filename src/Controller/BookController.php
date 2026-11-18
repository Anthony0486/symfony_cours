<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BookService;

class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService
        ) {}
    #[Route(path:"/livres", name:"app_books")]
    public function showAllBooks(): Response{
        return $this->render("book/show_all_books.html.twig", [
            "books"=> $this->bookService->getBooks()
        ]);
    }
    #[Route(path:"/livres/{id}", name:"app_book_id")]
    public function showBookById(int $id): Response{ 
        return $this->render("book/show_book_id.html.twig", [
            "book"=> $this->bookService->getBookById($id)
        ]);
    }
}