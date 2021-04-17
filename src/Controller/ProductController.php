<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Product;
use App\Form\CommentaryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="product")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @return Response
     */
    public function products(ProductRepository $product, CategoryRepository $category): Response
    {
        $products = $product->findBy([],['date_update' => 'DESC']);
        $categories = $category->findAll();
        return $this->render('product/index.html.twig', ['products' => $products,'categories' => $categories]);
    }

    /**
     * @Route("/all", name="all")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function all(ProductRepository $product, CategoryRepository $category, PaginatorInterface $paginator, Request $request): Response
    {
        $products = $product->findBy([],['date_add' => 'DESC']);
        $categories = $category->findAll();
        $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 10);
        return $this->render('product/all.html.twig', ['products' => $products,'categories' => $categories, 'pagination' => $pagination]);
    }

    /**
     * @Route ("/sales", name="sales")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function sales(ProductRepository $product, CategoryRepository $category, PaginatorInterface $paginator, Request $request): Response
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('discount',0))->orderBy(['date_add' => Criteria::DESC]);
        $categories = $category->findAll();
        $products = $product->matching($criteria);
        $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 10);
        return $this->render('product/sales.html.twig', ['products' => $products,'categories' => $categories, 'pagination' => $pagination]);
    }

    /**
     * @Route("/{cat}s", name="categoryRoute")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function catRoute(ProductRepository $product, CategoryRepository $category, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $category->findAll();
        $cat= $request->attributes->get('cat');
        $idCat = $category->findOneBy(['name' => $cat]);
        $products = $product->findBy(['category' => $idCat], ['date_add' => 'DESC']);

        $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 10);

        return $this->render('product/bycategory.html.twig', ['products' => $products,'categories' => $categories, 'category' => $cat, 'pagination' => $pagination]);
    }


    /**
     * @Route ("/{cat}s/{slug}", name="category-single")
     * @param Request $request
     * @param Product $product
     * @param CategoryRepository $category
     * @param EntityManagerInterface $manager
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function catSingle(Request $request, Product $product, CategoryRepository $category, EntityManagerInterface $manager, PaginatorInterface $paginator): Response
    {
        $criteria = new Criteria();
        $criteria->orderBy(['created_at' => Criteria::DESC]);

        $categories = $category->findAll();
        $commentaries = $product->getCommentaries();
        $commentary = new Commentary();

        $commentaries = $commentaries->matching($criteria);
        $pagination = $paginator->paginate($commentaries, $request->query->getInt('page', 1), 10);

        $form = $this->createForm(CommentaryType::class, $commentary);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $logged_user = $this->security->getUser();
            $commentary->setUser($logged_user);

            $now = new \DateTime('now');
            $commentary->setCreatedAt($now);

            $commentary->setProduct($product);

            $manager->persist($commentary);
            $manager->flush();

            $this->addFlash('message', 'Commentaire ajouté');
        }
        return $this->render('product/detail.html.twig', ['product' => $product,'categories' => $categories, 'form' => $form->createView(), 'commentaries' => $pagination]);
    }

    /**
     * @Route("/condition", name="conditions")
     * @param CategoryRepository $category
     * @return Response
     */
    public function conditions(CategoryRepository $category): Response
    {
        $categories = $category->findAll();
        return $this->render('partials/conditions.html.twig', ['categories' => $categories]);
    }

}
