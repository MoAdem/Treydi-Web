<?php

namespace App\Controller;
use App\Repository\ReponseRepository;
use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseAddType;
use App\Form\UpdateReponseType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;



class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse')]
    public function index(): Response
    {
        return $this->render('reponse/index.html.twig', [
            'controller_name' => 'ReponseController',
        ]);
    }
    #[Route('/reponse/add/{id}', name: 'app_reponseAdd', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine, Reclamation $reclamation): Response
    {
        $em = $doctrine->getManager();
        $req = new Reponse();
        $reclamation->setEtatReclamation("Traité");
        $req->setArchived(0); // set archived property to 0 (not archived)
        $req->setIdReclamation($reclamation);
        $req->setDateReponse(new DateTime());
        $form = $this->createForm(ReponseAddType::class, $req);
        $form->add('ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($req);
            $em->flush();
            return $this->redirectToRoute('app_reponseAdd', ['id' => $reclamation->getId()]);
        }
        return $this->renderForm('reponse/add_reponse.html.twig', ['formR' => $form]);
    }

    #[Route('/reponse/list/{id}', name: 'app_reponseList', methods: ['POST','GET'])]
    public function list(ManagerRegistry $doctrine,int $id): Response
    {


      //  $query = $repository->createQueryBuilder('r')
            //->where('r.archived = :archived')
           // ->andwhere('r.id_reclamation' =: id)
          //  ->setParameter('archived', 0)
          //  ->>setParameter('id', $id)
        //    ->getQuery();
        $repository = $doctrine->getRepository(Reponse::class);
        $listr = $repository->listReponseparReclamation($id);


        return $this->render('reponse/showReponse.html.twig', [
            'controller_name' => 'ReponseListController',
            'listr' => $listr,
        ]);

    }

    #[Route('/reponse/delete/{id}', name: 'app_reponseDelete', methods: ['POST', 'GET'])]
    public function deleteReclamation(Reponse $reponse, ManagerRegistry $doctrine): Response
    {
        $reponse->setArchived(1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('app_reponseList');
    }


    #[Route('/reponse/update/{id}', name: 'app_reponseUpdate')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $em = $doctrine->getManager();
        $reponse = $em->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Reclamation not found');
        }

        $form = $this->createForm(UpdateReponseType::class, $reponse);
        $form->add('modifier', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_reponseList');
        }

        return $this->renderForm('reponse/updateReponse.html.twig', ['formUr' => $form]);
    }





}
