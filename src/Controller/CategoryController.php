<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Form\CategoryType;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService
    ) {
    }

    #[Route(path: '/categories', name: 'app_categories')]
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
    public function new(Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $msg = $this->categoryService->saveCategory($category);
            $this->addFlash('succes', $msg);
            $this->redirectToRoute('app_categories_new');

        return $this->render('category/category_new.html.twig', [
            'form'->$this->$form,
            'msg'->$this->$msg ?? ""
        ]);
    }
}
}