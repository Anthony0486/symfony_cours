<?php

namespace App\Dto;
use App\Entity\Book;

final readonly class BookListDto
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $resume,
        public ?string $author,
        public ?\DateTime $publishAt,
        )
        {}
    public static function fromEntity(Book $book): self
    {
        return new self(
            id: $book->getId(),
            title: $book->getTitle(),
            resume: $book->getResume(),
            author: $book->getAuthor(),
            publishAt: $book->getPublishAt(),
        );
    }
}

