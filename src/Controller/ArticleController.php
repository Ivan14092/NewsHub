<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepository  $articleRepository,
        private readonly CategoryRepository $categoryRepository,
    )
    {
    }

    #[Route('', name: 'app_article_index')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $this->articleRepository->findLatest(20),
            'categories' => $this->categoryRepository->findAllOrdered(),
        ]);
    }

    #[Route('/{slug}', name: 'app_article_show', requirements: ['slug' => '[a-z0-9-]+'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'categories' => $this->categoryRepository->findAllOrdered(),
        ]);
    }
}
