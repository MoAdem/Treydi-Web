<?php

namespace App\Controller;

use App\Entity\Authors;
use App\Form\AuthorsType;
use App\Form\SearchAuthorsAdminType;
use App\Repository\AuthorsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors')]
class AuthorsController extends AbstractController
{
    #[Route('/', name: 'app_authors_index', methods: ['GET'])]
    public function index(AuthorsRepository $authorsRepository, Request $request): Response
    {
        $searchForm = $this->createForm(SearchAuthorsAdminType::class);
        $searchForm->handleRequest($request);
        $search = null;
        /*findBy archived falseµ/*/

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->get('search')->getData();
            $queryAuthorList = $authorsRepository->findByFullName($search, false);
            $authorList = $queryAuthorList;
        } else {
            $queryAuthorList = $authorsRepository->findByArchived(false);
            $authorList = $queryAuthorList;
        }
//        $authors=$authorsRepository->findBy(['archived' => false]);
        dump($authorList);
        return $this->render('authors/index.html.twig', [
            'authors' => $authorList,
            'searchForm' => $searchForm->createView(),
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_authors_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AuthorsRepository $authorsRepository): Response
    {
        $author = new Authors();
        $form = $this->createForm(AuthorsType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $authorsRepository->save($author, true);

            return $this->redirectToRoute('app_authors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('authors/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_authors_show', methods: ['GET'])]
    public function show(Authors $author): Response
    {
        return $this->render('authors/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_authors_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Authors $author, AuthorsRepository $authorsRepository): Response
    {
        $form = $this->createForm(AuthorsType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $authorsRepository->save($author, true);
            dump($form->getData());
            $message = 'Update réussie de l\'auteur : ' . $author->getFullName();
            $this->addFlash('update_message', $message);
            return $this->redirectToRoute('app_authors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('authors/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_authors_delete', methods: ['POST'])]
    public function delete(Request $request, Authors $author, AuthorsRepository $authorsRepository): Response
    {

            $authorsRepository->removeAuthor($author, true);
            $message = 'Suppression réussie de l\'auteur : ' . $author->getFullName();
            $this->addFlash('delete_message', $message);
        return $this->redirectToRoute('app_authors_index', [], Response::HTTP_SEE_OTHER);
    }
}
