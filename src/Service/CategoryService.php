<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getCategories(): array
    {
        return $this->categoryRepository->findAll() ?? [];
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }
    public function saveCategory(Category $category): string
    {
        $isNew = null === $category->getId();
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        return $isNew
            ? 'La catégorie a bien été ajoutée.'
            : 'La catégorie a bien été modifiée.';
    }

}