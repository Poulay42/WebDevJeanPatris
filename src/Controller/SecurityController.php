<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;

use App\Form\CategoryType;
use App\Form\ProfileType;
use App\Form\RegistrationType;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
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
     * @Route("/registration", name="registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @param CategoryRepository $category
     * @return Response
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, CategoryRepository $category) : Response
    {
        $categories = $category->findAll();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(empty($user->getImage()))
            {
                $user->setImage('default.png');
                $now = new \DateTime('now');
                $user->setUpdatedAt($now);
            }
            $slugify = new Slugify();
            $user->setSlug($slugify->slugify($user->getUsername()));
            $manager->persist($user);
            $user->setPassword($encoder->encodePassword($user,$form->get('password')->getData()));
            $manager->flush();
            $this->addFlash(
                'success',
                'Utilisateur correctement ajouté'
            );
            return $this->redirectToRoute('product');
        }
        return $this->render('security/registration.html.twig', ['form' => $form->createView(),'categories' => $categories]);
    }

    /**
     * @Route("/login", name="login")
     * @param CategoryRepository $category
     * @return Response
     */

    public function login(CategoryRepository $category): Response
    {
        $categories = $category->findAll();
        return $this->render('security/login.html.twig',['categories' => $categories]);
    }

    /**
     * @Route("/logout", name="logout")
     */

    public function logout()
    {
    }

    /**
     * @Route("/profile/{slug}", name="profile")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param CategoryRepository $category
     * @param User $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager, CategoryRepository $category, User $user, UserPasswordEncoderInterface $passwordEncoder) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $logged_user = $this->security->getUser();

        if($logged_user == null)
            return $this->redirectToRoute('login');

        $userTry = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $logged_user->getUsername()]);

        if($user->getSlug() != $userTry->getSlug())
            return $this->redirectToRoute('profile', array('slug' => $userTry->getSlug()));

        $categories = $category->findAll();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $slugify = new Slugify();
            $user->setSlug($slugify->slugify($user->getUsername()));
            if($form->get('password')->getData() != null)
                $user->setPassword($passwordEncoder->encodePassword($user,$form->get('password')->getData()));
            if($form->get('imageFile')->getData() != null)
            {
                $user->setImageFile($form->get('imageFile')->getData());
                $user->setImage($user->getImageFile());
            }
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/profile.html.twig', ['form' => $form->createView(),'categories' => $categories, 'user' => $user]);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @param Request $request
     * @param ProductRepository $product
     * @param PaginatorInterface $paginator
     * @param CategoryRepository $category
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function dashboard(Request $request, ProductRepository $product, PaginatorInterface $paginator, CategoryRepository $category, EntityManagerInterface $manager ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $logged_user = $this->security->getUser();

        if($logged_user == null)
            return $this->redirectToRoute('login');
        if($logged_user->getRoles() == 'ROLE_USER')
            return $this->redirectToRoute('product');

        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        $cat = new Category();
        if($form->isSubmitted() && $form->isValid())
        {
            $cat->setName($form->get('name')->getData());
            $manager->persist($cat);
            $manager->flush();
        }

        $categories = $category->findAll();
        $products = $product->findAll();

        $pagination = $paginator->paginate($products, $request->query->getInt('page', 1), 10);

        return $this->render('admin/dashboard.html.twig', ['form' => $form->createView(),'categories' => $categories, 'products' => $products, 'pagination' => $pagination]);
    }

    /**
     * @Route ("/roles", name="adminRole")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @return Response
     */
    public function adminRole(ProductRepository $product, CategoryRepository $category): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $logged_user = $this->security->getUser();

        if($logged_user == null)
            return $this->redirectToRoute('login');
        if($logged_user->getRoles() != 'ROLE_SUPER_ADMIN')
            return $this->redirectToRoute('product');

        $categories = $category->findAll();

        return $this->render('admin/partials/roles.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route ("/newProduct", name="newProduct")
     * @param ProductRepository $product
     * @param CategoryRepository $category
     * @return Response
     */
    public function newProduct(ProductRepository $product, CategoryRepository $category): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $logged_user = $this->security->getUser();

        if($logged_user == null)
            return $this->redirectToRoute('login');
        if($logged_user->getRoles() != 'ROLE_ADMIN')
            return $this->redirectToRoute('product');

        $categories = $category->findAll();

        return $this->render('admin/partials/new_product.html.twig', ['categories' => $categories]);
    }
}
