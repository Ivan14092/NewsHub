<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository  $articleRepository,
        private readonly PaginatorInterface $paginator,
    )
    {
    }

    #[Route('', name: 'app_article_index')]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q', '');

        if ($search) {
            $query = $this->articleRepository->findBySearch($search);
        } else {
            $query = $this->articleRepository->createQueryBuilder('a')
                ->orderBy('a.createdAt', 'DESC')
                ->getQuery();
        }

        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/{slug}', name: 'app_article_show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
