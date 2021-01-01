<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_view", requirements={"id"="\d+"})
     */
    public function view(Article $article, Request $request): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser())
            ->setArticle($article)
            ->setCreatedAt(new \DateTime());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_view', ['id' => $article->getId()]);
        }
        else {
            return $this->render('blog/view.html.twig', [
                'article' => $article,
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * @Route("/blog/new", name="blog_new")
     * @Route("/blog/{id}/edit", name="blog_edit", requirements={"id"="\d+"})
     */
    public function form(Article $article = null, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$article) {
            $article = new Article();
            $article->setCreatedAt(new \DateTime())
                    ->setUser($this->getUser());
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
