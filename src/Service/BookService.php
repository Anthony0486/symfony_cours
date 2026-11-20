<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Dto\BookListDto;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private EntityManagerInterface $entityManager
    ) {
    }
    public function getBooks(): array
    {
        return $this->bookRepository->findAll() ?? [];
    }
    public function getDtoBooks(): array
    {
        $books = $this->bookRepository->findAll();
        $dtos = [];
        foreach ($books as $book) {
            $dtos[] = BookListDto::fromEntity($book);
        }
        return $dtos;
    }
    public function getBookById(int $id): ?Book
    {
        return $this->bookRepository->find($id);
    }
    public function getBookByName(string $name): ?Book
    {
        return $this->bookRepository->findOneBy(["name" => $name]);
    }

    public function getBooksByAfterPublishAt(\DateTimeInterface $date): array
    {
        return $this->bookRepository->findPublishedAfter($date);
    }
    public function getBooksByCategory(string $category): array
    {
        return $this->bookRepository->findByCategory($category);
    }
    public function getBooksByTitle(string $title): ?book
    {
        return $this->bookRepository->findByTitle($title);
    }
        public function saveBook(Book $book): string
    {
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return "Le livre à bien été ajoutée";
    }
}