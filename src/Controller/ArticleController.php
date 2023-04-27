<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleRatings;
use App\Form\ArticleRatingsType;
use App\Form\ArticleType;
use App\Form\SearchArticlesAdminType;
use App\Form\SearchArticlesFormType;
use App\Repository\ArticleRatingsRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{

    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {

        //findByArchived(false) is the same as findBy(['archived' => false])
         /*   $queryArticleList = $articleRepository->findByArchived(false);
            $articleList = $queryArticleList;
         */

        // SEARCH FORM
        $searchForm = $this->createForm(SearchArticlesAdminType::class);
        $searchForm->handleRequest($request);
        $search = '';
        $date_publication = null;
        // HANDLE SEARCH FORM
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('search')->getData();
            $date_publication = $searchForm->get('date_publication')->getData();
            if ($date_publication instanceof \DateTime) {
                $date_publication = $date_publication->format('Y-m-d');
            }
//            $archived = $searchForm->get('archived')->getData();
            $queryArticleList = $articleRepository->findByTitleAndDescriptionAndDateI($search, $date_publication, false);
            $articleList = $queryArticleList;
        } else {
            $queryArticleList = $articleRepository->findByArchived(false);
            $articleList = $queryArticleList;
        }
        dump($articleList);
        return $this->render('article/index.html.twig', [
            //find articles that are not archived
            /*'articles' => $articleRepository->findBy(['archived' => false]),*/
            'searchForm'=> $searchForm->createView(),
            'articles' => $articleList,
            'search' => $search,
            'date_publication' => $date_publication,
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->saveA($article,$this->getUser(),  true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleRepository->savea($article,$this->getUser(),true);
            $message = 'Update réussie de l\'article : ' . $article->getId();
            $this->addFlash('update_message', $message);
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
            $articleRepository->removeA($article, true);
            /* redirect to route 'app_article_index', [], Response::HTTP_SEE_OTHER and popup alert on page load */
            $message = 'Suppression réussie de l\'article : ' . $article->getId();
            $this->addFlash('delete_message', $message);

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }



  /*add a new article rating in the show page*/



}
