<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BookService;
use App\Entity\Book;
use App\Form\BookFormType;
use App\Dto\BookDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService
    ) {
    }

    #[Route(path: "/books", name: "app_books")]
    #[IsGranted('ROLE_USER', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
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

    #[Route(path: '/book/{id}', name: 'app_book')]
    public function showBookById(int $id): Response
    {
        return $this->render('book/show_book_id.html.twig', [
            'book' => $this->bookService->getBookById($id)
        ]);
    }

    #[Route(path: '/books/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function new(Request $request): Response
    {
        $book = new Book();

        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $msg = $this->bookService->saveBook($book);

            $this->addFlash('succes', $msg);

            return $this->redirectToRoute('app_books');
        }
        return $this->render('book/book_new.html.twig', [
            'form' => $form
        ]);

    }
}