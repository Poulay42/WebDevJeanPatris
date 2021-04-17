<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/product", name="admin_product")
     */
    public function products(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminProductController',
        ]);
    }
}
