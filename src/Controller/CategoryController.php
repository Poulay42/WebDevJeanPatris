<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @return Response
     */
    public function cate(ProductRepository $product, CategoryRepository $category): Response
    {
        $products = $product->findBy([],['date_update' => 'DESC']);
        $categories = $category->findAll();
        return $this->render('category/index.html.twig', ['products' => $products,'categories' => $categories]);
    }
}
