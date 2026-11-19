
<?php

namespace App\Dto;
use App\Entity\Book;

final readonly class BookDetailDto
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $resume,
        public ?string $author,
        public array $categories,
        public ?string $userEmail,
        )
        {}
    public static function fromEntity(Book $book): self
    {
        return new self(
            id: $book->getId(),
            title: $book->getTitle(),
            resume: $book->getResume(),
            author: $book->getAuthor(),
            categories: $book->getCategories(),
            userEmail: $book->getUser(),
        );
    }
}
