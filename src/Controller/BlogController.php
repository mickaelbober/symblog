<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\View;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\LikeRepository;
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
        $criteria = [ 'published' => 1 ];
        $articles = $paginator->paginate(
            $repository->findBy($criteria),
            $request->query->getInt('page', 1),
            16
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
        $this->denyAccessUnlessGranted('view', $article);
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
            $view = $article->getView() + 1;
            $article->setView($view);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $user = $this->getUser();
            if ($user) {
                $view = new View();
                $view->setArticle($article);
                $view->setUser($user);
                $manager->persist($view);
            }
            $manager->flush();
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
        if (!$article) {
            $article = new Article();
            $article->setCreatedAt(new \DateTime())
                    ->setUser($this->getUser())
                    ->setView(0)
                    ->setPublished(1);
        }
        $this->denyAccessUnlessGranted('edit', $article);
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

    /**
     * @Route("/blog/{id}/like", name="blog_like", requirements={"id"="\d+"})
     */
    public function like(Article $article, LikeRepository $repository): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => 'non autorisé'
            ],
            403);
        }
        if ($article->isLikedByUser($user)) {
            $like = $repository->findOneBy([
                'article' => $article,
                'user' => $user
            ]);            
            $manager->remove($like);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'like bien supprimé',
                'likes' => $repository->count([
                    'article' => $article
                ])
            ],
            200);
        }
        $like = new Like();
        $like->setArticle($article);
        $like->setUser($user);
        $manager->persist($like);
        $manager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté',
            'likes' => $repository->count([
                'article' => $article
            ])
        ],
        200);
    }
}
