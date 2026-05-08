<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface       $slugger,
        private readonly ArticleRepository      $articleRepository,
    )
    {
    }

    public function create(
        string   $title,
        string   $content,
        Category $category,
        User     $author,
    ): Article
    {
        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setCategory($category);
        $article->setAuthor($author);
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setSlug($this->generateUniqueSlug($title));

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    public function update(Article $article, string $title, string $content): Article
    {
        if ($article->getTitle() !== $title) {
            $article->setSlug($this->generateUniqueSlug($title));
        }

        $article->setTitle($title);
        $article->setContent($content);
        $article->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $article;
    }

    public function delete(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }

    private function generateUniqueSlug(string $title): string
    {
        $baseSlug = strtolower($this->slugger->slug($title)->toString());
        $slug = $baseSlug;
        $counter = 1;

        while ($this->articleRepository->findBySlug($slug) !== null) {
            $slug = sprintf('%s-%d', $baseSlug, $counter);
            $counter++;
        }

        return $slug;
    }
}