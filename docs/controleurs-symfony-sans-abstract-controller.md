# Guide : Contrôleurs Symfony sans `AbstractController` (Invokables & Modernes)

Ce guide présente plusieurs façons de concevoir des contrôleurs Symfony sans hériter de `AbstractController` : méthode `__invoke()`, attributs `#[Route]` et `#[Template]`, injection de services et, depuis Symfony 7.4, `ControllerHelper`. Les exemples reposent principalement sur Symfony 7.4 / 8 ; certaines fonctionnalités existent dans des versions antérieures.

> **Compatibilité :** depuis Symfony 7.3, `#[Route]` suffit en configuration standard pour identifier automatiquement une classe comme contrôleur service. `#[AsController]` reste utilisable pour expliciter cette intention ou pour d’autres configurations. `ControllerHelper` apparaît avec Symfony 7.4.

---

## 1. Structure de base d'un contrôleur invokable

Sans `AbstractController`, la classe est un simple objet PHP (POPO). `#[AsController]` permet de désigner explicitement une classe comme contrôleur Symfony. **Il n'est pas indispensable dans une application Symfony 7.3+ utilisant les routes par attributs avec la configuration standard** : `#[Route]` suffit alors à identifier le contrôleur et à permettre la résolution des arguments.

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/bonjour/{nom}', name: 'app_bonjour', methods: ['GET'])]
final class BonjourController
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(string $nom): Response
    {
        $html = $this->twig->render('bonjour/index.html.twig', [
            'nom' => $nom,
        ]);

        return new Response($html);
    }
}
```

### Points clés
* **`#[AsController]` :** rend l'intention explicite ; il est facultatif avec `#[Route]` dans la configuration standard de Symfony 7.3+.
* **Injection de dépendances :** Les services (`Twig\Environment`, `EntityManagerInterface`, etc.) doivent être injectés explicitement via le constructeur ou les paramètres de méthode.
* **Approche ADR (Action-Domain-Responder) :** une classe invocable porte une action (`__invoke`) ; elle peut toutefois être associée à plusieurs routes. Il s'agit d'un choix de conception, non d'une obligation Symfony.

---

## 2. Rendre une vue Twig sans `AbstractController`

Trois approches permettent d'afficher un template Twig :

### Option A : Utiliser l'attribut `#[Template]` (solution concise pour les pages HTML)
Votre méthode retourne directement un tableau (`array`) de variables. Symfony intercepte ce tableau et génère l'objet `Response` automatiquement.

```php
<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/accueil', name: 'app_accueil')]
final class AccueilController
{
    #[Template('accueil/index.html.twig')]
    public function __invoke(): array
    {
        return [
            'titre' => 'Bienvenue',
        ];
    }
}
```

### Option B : Injecter `Twig\Environment`
Voir l'exemple de la section 1 (`new Response($this->twig->render(...))`).

### Option C : Créer un trait personnalisé (`RenderTrait`)
Cet exemple illustre le mécanisme des traits et permet de conserver une syntaxe proche de `$this->render()`. **Ce n'est pas l'approche recommandée par défaut** : elle reproduit une partie des outils de Symfony et doit être maintenue. Pour Symfony 7.4+, voir `ControllerHelper` ci-dessous.

Pour l'exemple pédagogique :

```php
<?php

namespace App\Controller\Support;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

trait RenderTrait
{
    private Environment $twig;

    #[Required]
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $content = $this->twig->render($view, $parameters);

        $response ??= new Response();
        $response->setContent($content);

        return $response;
    }
}
```

---

## 3. Contrôleur invokable vs Contrôleur multi-méthodes

### Plusieurs URLs sur un seul `__invoke`
Il est possible d'empiler plusieurs attributs `#[Route]` sur un contrôleur invokable s'ils partagent exactement la même logique :

```php
#[AsController]
#[Route('/', name: 'app_home')]
#[Route('/accueil', name: 'app_accueil')]
final class HomeController
{
    public function __invoke(): Response
    {
        return new Response("Page d'accueil");
    }
}
```

### Comparaison des architectures

| Critère | Contrôleur Invokable (`__invoke`) | Contrôleur Classique (multi-méthodes) |
| :--- | :--- | :--- |
| **Nombre d'actions** | **Une seule action** par fichier. | **Plusieurs actions** liées (`list`, `show`, `edit`, `delete`). |
| **Injection de dépendances** | Par constructeur ou par argument de `__invoke()`. | Par constructeur ou par arguments des différentes actions ; elle n'est pas nécessairement plus lourde. |
| **Cas d'usage idéal** | API REST, pages complexes, actions métier, architecture ADR. | CRUD simple, regroupement logique d'actions légères. |

### Contrôleur multi-méthodes sans `AbstractController` (Autowiring d'action)
Si vous regroupez plusieurs méthodes dans un contrôleur sans `AbstractController`, injectez les services directement dans les paramètres de chaque méthode et ajoutez `#[Template]` sur chaque méthode rendant une vue :

```php
<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/articles', name: 'article_')]
final class ArticleController
{
    #[Route('', name: 'list', methods: ['GET'])]
    #[Template('article/list.html.twig')]
    public function list(ArticleRepository $repository): array
    {
        return [
            'articles' => $repository->findAll(),
        ];
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[Template('article/show.html.twig')]
    public function show(Article $article): array
    {
        return [
            'article' => $article,
        ];
    }
}
```

---

## 4. Gestion d'un formulaire et redirection

Pour traiter un formulaire sans `AbstractController`, injectez `FormFactoryInterface` et `UrlGeneratorInterface`. Le type de retour `Response|array` permet de retourner soit les variables de la vue (tableau traité par `#[Template]`), soit une redirection (`RedirectResponse`). **Attention :** dans cet exemple, `#[Template]` ne garantit pas le statut HTTP 422 d'un formulaire soumis mais invalide.

```php
<?php

namespace App\Controller\Article;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
#[Route('/articles/nouveau', name: 'article_create', methods: ['GET', 'POST'])]
final class CreateArticleController
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Template('article/create.html.twig')]
    public function __invoke(Request $request): Response|array
    {
        $article = new Article();
        $form = $this->formFactory->create(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            // Ajouter un message flash sans AbstractController
            $request->getSession()->getFlashBag()->add('success', 'Article publié avec succès.');

            $url = $this->urlGenerator->generate('article_list');
            return new RedirectResponse($url);
        }

        // Le tableau est transmis au template Twig.
        // Le code HTTP 422 n'est pas automatiquement garanti ici.
        return [
            'form' => $form->createView(),
        ];
    }
}
```

---

## 5. Validation et Mapping automatique avec les DTO (`#[MapRequestPayload]` & `#[MapQueryString]`)

Les attributs `#[MapRequestPayload]` (introduit en Symfony 6.3) et `#[MapQueryString]` (Symfony 6.3) permettent de construire et de valider des DTO (Data Transfer Object) depuis les données HTTP, sans injecter manuellement `Request`, `SerializerInterface` ou `ValidatorInterface` dans les actions. La validation s'applique **aux contraintes effectivement définies** ; elle ne remplace pas les règles métier.

Pour `MapRequestPayload`, les erreurs de validation produisent par défaut une réponse **422** ; les erreurs de désérialisation et de format peuvent produire d'autres statuts (**400** ou **415**, selon le cas).

### A. Corps de requête (`#[MapRequestPayload]`) — POST / PUT / PATCH

**1. Le DTO (`src/Dto/CreateArticleDto.php`) :**

```php
<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateArticleDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
        #[Assert\Length(min: 5, max: 150)]
        public string $titre,

        #[Assert\NotBlank(message: 'Le contenu ne peut pas être vide.')]
        public string $contenu,
    ) {
    }
}
```

**2. Le contrôleur (`src/Controller/Api/CreateArticleController.php`) :**

```php
<?php

namespace App\Controller\Api;

use App\Dto\CreateArticleDto;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/articles', name: 'api_article_create', methods: ['POST'])]
final class CreateArticleController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(#[MapRequestPayload] CreateArticleDto $dto): JsonResponse
    {
        // Le DTO a été construit et contrôlé selon les contraintes déclarées.
        // Une violation de validation donne par défaut HTTP 422.
        $article = new Article();
        $article->setTitre($dto->titre);
        $article->setContenu($dto->contenu);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $article->getId(),
            'status' => 'created',
        ], Response::HTTP_CREATED);
    }
}
```

### B. Paramètres d'URL (`#[MapQueryString]`) — GET (Filtres / Pagination)

**1. Le DTO (`src/Dto/ArticleFilterDto.php`) :**

```php
<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ArticleFilterDto
{
    public function __construct(
        #[Assert\Positive]
        public int $page = 1,

        #[Assert\Length(min: 2, max: 50)]
        public ?string $recherche = null,
    ) {
    }
}
```

**2. Le contrôleur (`src/Controller/Api/ListArticlesController.php`) :**

```php
<?php

namespace App\Controller\Api;

use App\Dto\ArticleFilterDto;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/articles', name: 'api_article_list', methods: ['GET'])]
final class ListArticlesController
{
    public function __construct(
        private readonly ArticleRepository $repository,
    ) {
    }

    public function __invoke(#[MapQueryString] ArticleFilterDto $filtres = new ArticleFilterDto()): JsonResponse
    {
        $articles = $this->repository->searchByFilters($filtres->page, $filtres->recherche);

        // Ici, searchByFilters() doit fournir des données sérialisables en JSON
        // (par exemple des tableaux de scalaires ou des DTO convertis en tableaux).
        return new JsonResponse($articles);
    }
}
```

---

## 6. Utiliser `ControllerHelper` sans hériter de `AbstractController` (Symfony 7.4+)

Depuis **Symfony 7.4**, `Symfony\Bundle\FrameworkBundle\Controller\ControllerHelper` expose les méthodes utilitaires de `AbstractController` comme des méthodes publiques. Cela permet de privilégier la **composition** plutôt que l'héritage.

### Option D : Injecter `ControllerHelper`

```php
<?php

namespace App\Controller\Article;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles/aide', name: 'article_helper', methods: ['GET'])]
final readonly class ArticleHelperController
{
    public function __construct(
        private ControllerHelper $helper,
    ) {}

    public function __invoke(): Response
    {
        return $this->helper->render('article/help.html.twig', [
            'titre' => 'Utiliser ControllerHelper',
        ]);
    }
}
```

Ce contrôleur n'étend aucune classe. Il utilise les méthodes de `ControllerHelper` directement, avec un service injecté dans son constructeur.

### Injection ciblée : `#[AutowireMethodOf]`

Symfony permet aussi d'injecter **uniquement la méthode nécessaire**, sous forme de `Closure`. Cela limite les dépendances exposées par le contrôleur.

```php
<?php

namespace App\Controller\Article;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerHelper;
use Symfony\Component\DependencyInjection\Attribute\AutowireMethodOf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles/infos', name: 'article_infos', methods: ['GET'])]
final readonly class ArticleInfoController
{
    public function __construct(
        #[AutowireMethodOf(ControllerHelper::class)]
        private \Closure $render,
    ) {}

    public function __invoke(): Response
    {
        return ($this->render)('article/infos.html.twig', [
            'titre' => 'Informations sur les articles',
        ]);
    }
}
```

Le nom de la propriété `$render` désigne la méthode `render()` à injecter. On peut appliquer le même principe à d'autres raccourcis de `ControllerHelper`, notamment `redirectToRoute()`.

### Comparer les solutions

| Technique | Avantage | Limite / contexte |
| :--- | :--- | :--- |
| `#[Template]` + tableau | Très concis pour une page HTML simple | Moins adapté si l'on veut piloter précisément la réponse HTTP |
| `Twig\Environment` + `Response` | Dépendances explicites ; contrôle de la réponse | Un peu plus de code |
| Trait `RenderTrait` maison | Démonstration des traits | Du code d'infrastructure à maintenir soi-même |
| `ControllerHelper` | Retrouve les raccourcis Symfony par composition | **Symfony 7.4+** ; dépendance à FrameworkBundle |
| `#[AutowireMethodOf]` + `ControllerHelper` | Injecte seulement le helper utile | **Symfony 7.4+** dans cet usage ; syntaxe un peu plus avancée |

> **À retenir :** éviter `AbstractController` ne signifie pas forcément rendre l'application indépendante de Symfony. Avec `ControllerHelper`, on évite l'héritage, mais on utilise toujours une API du framework. Pour un cours, cela illustre surtout le choix entre héritage, composition et injection de dépendances.

---

## 7. Tableau récapitulatif des équivalences

| Raccourci dans `AbstractController` | Équivalent sans `AbstractController` |
| :--- | :--- |
| `$this->render('vue.html.twig', $data)` | `#[Template('vue.html.twig')]` + `return $data;`, `Twig\Environment` ou `ControllerHelper::render()` (7.4+) |
| `$this->json($data, 200)` | `new JsonResponse($data, 200)` pour des données directement sérialisables ; sinon `SerializerInterface` / DTO de sortie |
| `$this->createForm(Type::class, $obj)` | `$formFactory->create(Type::class, $obj)` via `FormFactoryInterface` |
| `$this->redirectToRoute('route_name')` | `return new RedirectResponse($urlGenerator->generate('route_name'));` |
| `$this->addFlash('type', 'message')` | `$request->getSession()->getFlashBag()->add('type', 'message');` |
| `$this->getUser()` | Attribut `#[CurrentUser] ?User $user` dans les arguments de la méthode |
| `$this->denyAccessUnlessGranted('ROLE')` | `#[IsGranted('ROLE')]` pour une vérification déclarative ; `AuthorizationCheckerInterface` pour une vérification impérative ou conditionnelle |

---

## 8. Points de vigilance et ressources

- Les exemples supposent que les routes par attributs et l'autowiring des contrôleurs sont configurés (configuration Symfony standard).
- `#[Template]` est adapté aux réponses HTML classiques ; pour choisir explicitement un code HTTP, les en-têtes ou le type de réponse, construire une `Response` reste une bonne solution.
- `new JsonResponse($entities)` **ne remplace pas** la sérialisation Symfony : privilégier des données scalaires ou des DTO de sortie correctement normalisés.
- `#[IsGranted]` vérifie une permission lors du traitement de l'action ; `AuthorizationCheckerInterface` permet un contrôle explicite en cours de méthode.
- `AbstractController` reste une solution parfaitement valide. L'objectif est de comparer plusieurs styles, pas d'interdire l'héritage.

### Documentation officielle

- [Symfony — Controllers](https://symfony.com/doc/7.4/controller.html)
- [Symfony — Contrôleurs enregistrés comme services](https://symfony.com/doc/7.4/controller/service.html)
- [Symfony 7.3 — Enregistrement automatique via `#[Route]`](https://symfony.com/blog/new-in-symfony-7-3-routing-improvements)
- [Symfony 7.4 — ControllerHelper](https://symfony.com/blog/new-in-symfony-7-4-decoupled-controller-helpers)
- [Symfony — Templates Twig](https://symfony.com/doc/7.4/templates.html)
