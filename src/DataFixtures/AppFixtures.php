<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SluggerInterface $slugger,
    ){}
    public function load(ObjectManager $manager): void
    {
     $categories =[];
        foreach (['Технології', 'Спорт', 'Наука', 'Культура'] as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug(strtolower($this->slugger->slug($name)->toString()));
            $category->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);
            $categories[] = $category;
    }
        $user = new User();
        $user->setEmail('admin@newshub.com');
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($user);

        $articles = [
            ['Symfony 7 — що нового', 'Symfony 7 приніс багато змін у компонентах фреймворку.', 0],
            ['PHP 8.3 — огляд змін', 'PHP 8.3 містить нові атрибути та покращення типізації.', 0],
            ['Збірна України на Євро', 'Збірна України готується до важливих матчів турніру.', 1],
            ['Відкриття нового музею', 'У Києві відкрився сучасний музей сучасного мистецтва.', 3],
            ['Нові дослідження Mars', 'NASA опублікувало нові дані з марсохода Perseverance.', 2],
        ];

        foreach ($articles as [$title, $content, $categoryIndex]) {
            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setSlug(strtolower($this->slugger->slug($title)->toString()));
            $article->setCategory($categories[$categoryIndex]);
            $article->setAuthor($user);
            $article->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($article);
        }

        $manager->flush();
    }
}