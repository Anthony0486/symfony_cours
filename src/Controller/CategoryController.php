<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Form\CategoryType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    #[Route(path: '/categories', name: 'app_categories')]
    #[IsGranted('ROLE_USER', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function showAllCategories(): Response
    {
        //Récupération de toutes les catégories de la BDD
        $categories = $this->categoryService->getCategories();

        return $this->render('category/show_all_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function showCategoryById(int $id): Response
    {
        return $this->render('category/show_category_id.html.twig', [
            "category" => $this->categoryService->getCategoryById($id)
        ]);
    }
    #[Route(path: '/categories/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function new(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $msg = $this->categoryService->saveCategory($category);

            $this->addFlash('succes', $msg);

            return $this->redirectToRoute('app_categories_new');
        }
        return $this->render('category/category_new.html.twig', [
            'form' => $form
        ]);

    }
    #[Route(path: '/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'êtes pas autorisé à accéder à cette page')]
    public function edit(Request $request, Category $category): Response
    {

        $form = $this->createForm(CategoryType::class, $category, ['is_edit' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $msg = $this->categoryService->saveCategory($category);

            $this->addFlash('succes', $msg);

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('category/category_new.html.twig', [
            'form' => $form,
            'is_edit' => true,
        ]);
    }
}