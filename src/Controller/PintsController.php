<?php

namespace App\Controller;

use App\Entity\Pints;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;



class PintsController extends AbstractController
{

    #[Route('/pints', name: 'app_pints')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PintsController.php',
        ]);
    }

    #[Route('/list', name: 'app_pints_list')]
    public function show(): Response
    {
        return $this->render('Pints\list.html.twig' );
    }

    #[Route('/add', name: 'app_pints_add')]
    public function add(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $pints = new Pints();
        $pints->setName('pints 1');
        $pints->setDescription("desc 1");
        $entityManager->persist($pints);
        $entityManager->flush();

        return $this->render('Pints\list.html.twig' );
    }

    #[Route('/edit/{id}', name: 'app_pints_edit')]
    public function edit(ManagerRegistry $doctrine, int $id): Response
    {
        $pints = $doctrine->getRepository(Pints::class)->find($id);
        $entityManager = $doctrine->getManager();
       
        $pints->setName('pints 1');
        $pints->setDescription("desc 6");
        $entityManager->persist($pints);
        $entityManager->flush();

        return $this->render('Pints\list.html.twig' );
    }

    
}


