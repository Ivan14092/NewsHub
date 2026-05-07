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
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Категорії
        $categoriesData = [
            'Технології', 'Спорт', 'Наука', 'Культура',
            'Політика', 'Економіка', 'Здоров\'я', 'Освіта',
        ];

        $categories = [];
        foreach ($categoriesData as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug(strtolower($this->slugger->slug($name)->toString()));
            $category->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);
            $categories[] = $category;
        }

        $admin = new User();
        $admin->setEmail('admin@newshub.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin);

        $authors = [$admin];
        foreach (['ivan', 'Denis', 'mykola', 'Nadya'] as $username) {
            $user = new User();
            $user->setEmail($username . '@newshub.com');
            $user->setUsername($username);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);
            $authors[] = $user;
        }

        $articles = [
            ['Symfony 7 — що нового у фреймворку', 'Symfony 7 приніс багато змін у компонентах фреймворку. Розглянемо найважливіші з них.', 0],
            ['PHP 8.3 — огляд нових можливостей', 'PHP 8.3 містить нові атрибути та покращення типізації. Що варто знати кожному розробнику.', 0],
            ['Docker для PHP розробників', 'Docker спрощує розробку та деплой PHP додатків. Розглянемо основні концепції.', 0],
            ['Збірна України на Євро 2024', 'Збірна України готується до важливих матчів турніру. Аналіз команди та шанси на успіх.', 1],
            ['Формула 1 — підсумки сезону', 'Захоплюючий сезон Формули 1 завершився несподіваними результатами.', 1],
            ['Баскетбол — чемпіонат України', 'Огляд найцікавіших матчів чемпіонату України з баскетболу.', 1],
            ['Нові дослідження Mars', 'NASA опублікувало нові дані з марсохода Perseverance. Що знайшли вчені?', 2],
            ['Штучний інтелект у медицині', 'Як AI змінює діагностику та лікування захворювань у сучасній медицині.', 2],
            ['Кліматичні зміни 2024', 'Нові дані про кліматичні зміни викликають занепокоєння вчених по всьому світу.', 2],
            ['Відкриття нового музею у Києві', 'У Києві відкрився сучасний музей сучасного мистецтва. Що можна побачити?', 3],
            ['Українське кіно на міжнародних фестивалях', 'Українські фільми здобувають визнання на престижних міжнародних кінофестивалях.', 3],
            ['Книжковий форум у Львові', 'Щорічний книжковий форум у Львові зібрав тисячі любителів літератури.', 3],
            ['Вибори у США — прогнози', 'Експерти аналізують шанси кандидатів на президентських виборах у США.', 4],
            ['Реформи в українській освіті', 'Нові освітні реформи змінюють підхід до навчання в українських школах.', 7],
            ['Економіка України у 2024', 'Аналіз економічних показників України та прогнози на майбутнє.', 5],
            ['Здорове харчування — міф чи реальність', 'Дієтологи розповідають про принципи здорового харчування та популярні міфи.', 6],
            ['Стартапи в Україні', 'Огляд найуспішніших українських стартапів які здобули міжнародне визнання.', 0],
            ['React vs Vue — що обрати', 'Порівняння двох популярних JavaScript фреймворків для фронтенд розробки.', 0],
            ['Медитація та продуктивність', 'Як медитація допомагає підвищити концентрацію та продуктивність.', 6],
            ['Університети України у рейтингах', 'Які українські університети потрапили до світових освітніх рейтингів.', 7],
        ];

        foreach ($articles as $index => [$title, $content, $categoryIndex]) {
            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setSlug(strtolower($this->slugger->slug($title)->toString()));
            $article->setCategory($categories[$categoryIndex]);
            $article->setAuthor($authors[$index % count($authors)]);
            $article->setCreatedAt(new \DateTimeImmutable('-' . $index . ' days'));
            $manager->persist($article);
        }

        $manager->flush();
    }
}