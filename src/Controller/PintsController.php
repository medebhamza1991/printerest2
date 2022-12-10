<?php

namespace App\Controller;

use App\Entity\Pints;
use App\Form\PintsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Security;

class PintsController extends AbstractController
{
    private $em;
    private $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('/', name: 'app_pints')]
    public function index(): Response
    {
        return $this->render('Pints\index.html.twig');
    }

    #[Route('/list', name: 'app_pints_list')]
    public function show(ManagerRegistry $doctrine): Response
    {
        $pints = $doctrine->getRepository(Pints::class)->findAll();
        return $this->render('Pints\list.html.twig', [
            "pints" => $pints
        ]);
    }

    #[Route('/add', name: 'app_pints_add')]
    public function add(Request $request, FileUploader $fileUploader): Response
    {
        $user = $this->security->getUser();
        $pints = new Pints();
        $form = $this->createForm(PintsType::class, $pints);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pints = $form->getData();
            $name = $pints->getName();
            $description = $pints->getDescription();
            $pints->setName($name);
            $pints->setDescription($description);
            $pints->setUser($user);
            $pinFile = $form->get('picture')->getData();
            if ($pinFile)
            {
                $pinsFileName = $fileUploader->upload($pinFile);
                $pints->setPicture($pinsFileName);
            }
            $this->em->persist($pints);
            $this->em->flush();
            $this->addFlash(
                'success',
                'l\'article a été ajouté avec succés !'
            );
            return $this->redirectToRoute('app_pints_list');
        }


        return $this->render('Pints\add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_pints_edit')]
    public function edit(ManagerRegistry $doctrine,  FileUploader $fileUploader, Request $request, int $id): Response
    {
        $pints = $doctrine->getRepository(Pints::class)->find($id);

        $form = $this->createForm(PintsType::class, $pints);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pints = $form->getData();
            $name = $pints->getName();
            $description = $pints->getDescription();
            $pints->setName($name);
            $pints->setDescription($description);
            $pinFile = $form->get('picture')->getData();
            if ($pinFile)
            {
                $pinsFileName = $fileUploader->upload($pinFile);
                $pints->setPicture($pinsFileName);
            }
            $this->em->persist($pints);
            $this->em->flush();

            $this->addFlash(
                'success',
                'l\'article a été modifié avec succés !'
            );
            
            return $this->redirectToRoute('app_pints_list');
        }
        return $this->render('Pints\edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_pints_delete')]
    public function delete(ManagerRegistry $doctrine, EntityManagerInterface $em, Request $request, int $id): Response
    {
        $pints = $doctrine->getRepository(Pints::class)->find($id);
        if ($pints) {
            $this->em->remove($pints);
            $this->em->flush();
            $this->addFlash(
                'info',
                'l\'article a été supprimé avec succés !'
            );
        }else {
            $this->addFlash(
                'danger',
                'article inexistant !'
            );
        }

        return $this->redirectToRoute('app_pints_list');
    }

    #[Route('/detail/{id}', name: 'app_pints_detail')]
    public function detail(ManagerRegistry $doctrine, int $id): Response
    {
        $pint = $doctrine->getRepository(Pints::class)->find($id);
        return $this->render('Pints\detail.html.twig', [
            'pint' => $pint
        ]);
    }
}
