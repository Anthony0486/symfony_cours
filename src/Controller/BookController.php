<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BookService;
use App\Dto\BookDto;

class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    #[Route(path: "/books", name: "app_books")]
    // public function showAllBooks(): Response
    // {
    //     return $this->render("book/show_all_books.html.twig", [
    //         "books" => $this->bookService->getBooks()
    //     ]);
    // }
    public function showAllDtoBooks(): Response
    {
        return $this->render("book/show_all_books.html.twig", [
            "books" => $this->bookService->getDtoBooks(),
        ]);
    }

    #[Route(path: '/books/search/{title}', name: 'app_book_search')]
    public function showBooksByTitle(string $title): Response
    {
        return $this->render('book/show_all_books.html.twig', [
            'books' => $this->bookService->getBooksByTitle($title)
        ]);
    }

    #[Route(path: '/books/recent/{date}', name: 'app_book_recent')]
    public function showBooksByAfterPublishAt(\DateTime $date): Response
    {
        return $this->render('book/show_all_books.html.twig', [
            'books' => $this->bookService->getBooksByAfterPublishAt($date)
        ]);
    }

    #[Route(path: "/books/category/{category}", name: "app_book_category")]
    public function showBooksByCategory(string $category): Response
    {
        return $this->render("book/show_all_books.html.twig", [
            "books" => $this->bookService->getBooksByCategory($category)
        ]);
    }

    #[Route(path: '/books/{id}', name: 'app_book')]
    public function showBookById(int $id): Response
    {
        return $this->render('book/show_book_id.html.twig', [
            'book' => $this->bookService->getBookById($id)
        ]);
    }
}