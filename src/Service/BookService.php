<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;

class BookService {
    public function __construct(
        private readOnly BookRepository $bookRepository)
        {}
    public function getBooks(): array {
        return $this->bookRepository->findAll() ?? [];
    }
    public function getBookById(int $id): ?Book 
    {
        return $this->bookRepository->find($id);
    }
    public function getBookByName(string $name): ?Book
    {
        return $this->bookRepository->findOneBy(["name"=> $name]);
    }

    public function getRecentBooks(\DateTimeInterface $date): array
    {
        return $this->bookRepository->findPublishedAfter($date);
    }
    public function getBooksByCategory(string $category): array
    {
        return $this->bookRepository->findByCategory($category);
    }
}