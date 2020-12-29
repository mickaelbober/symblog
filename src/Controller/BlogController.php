<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_view", requirements={"id"="\d+"})
     */
    public function view(Article $article): Response
    {
        return $this->render('blog/view.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_new")
     * @Route("/blog/{id}/edit", name="blog_edit", requirements={"id"="\d+"})
     */
    public function form(Article $article = null, Request $request): Response
    {
        if (!$article) {
            $article = new Article();
            $article->setCreatedAt(new \DateTime());
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('blog/form.html.twig', [
                'form' => $form->createView()
            ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            return new Response('OK');
        }
        return $this->render('blog/modal.html.twig', [
            'form' => $form->createView()
        ]); 
    }
}
