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
        #[Route(path:"/livres/recent/{date}", name:"app_book_recent")]
    public function showRecentBooks(\dateTime $date): Response{
        return $this->render("book/show_recent_books.html.twig", [
            "recentBooks"=> $this->bookService->getRecentBooks($date)
        ]);
}
        #[Route(path:"/livres/category/{category}", name:"app_book_category")]
    public function showBooksByCategory(string $category): Response{
        return $this->render("book/show_books_category.html.twig", [
            "booksByCategory"=> $this->bookService->getBooksByCategory($category)
        ]);
}
    #[Route(path:"/livres/{id}", name:"app_book_id")]
    public function showBookById(int $id): Response{ 
        return $this->render("book/show_book_id.html.twig", [
            "book"=> $this->bookService->getBookById($id)
        ]);
    }

}