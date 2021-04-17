<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param CategoryRepository $category
     * @return Response
     */
    public function index(Request $request, \Swift_Mailer $mailer, CategoryRepository $category): Response
    {
        $categories = $category->findAll();
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();

            $message = (new \Swift_Message('Nouveau contact'))
                ->setFrom($contact['email'])
                ->setTo('thepoulay@gmail.com')
                ->setBody(
                    $this->renderView('contact/email.html.twig', compact('contact')),'text/html');

            $mailer->send($message);

            $this->addFlash('message', 'Le message a bien été envoyé');
            $this->redirectToRoute('product');
        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(), 'categories' => $categories
        ]);
    }
}
