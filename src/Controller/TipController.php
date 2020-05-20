<?php

namespace App\Controller;

use App\Entity\Tip;
use App\Entity\Tool;
use App\Form\TipType;
use App\Entity\Category;
use App\Repository\ThemeRepository;
use App\Service\FileUploader;
use App\Repository\TipRepository;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TipController extends AbstractController
{
    /**
     * @Route("{themeSlug}/{categorySlug}/{slug}/tips", name="tip_index", methods={"GET"})
     */
    public function index(TipRepository $tipRepository, Tool $tool, ThemeRepository $themeRepository): Response
    {
        return $this->render('tip/index.html.twig', [
            'tips' => $tipRepository->findBy(['tool' => $tool]),
            'tool' => $tool,
            'themes' => $themeRepository->findAll()
        ]);
    }

    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/{slug}/tips/new", name="tip_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool): Response
    {
        $tip = new Tip();
        $form = $this->createForm(TipType::class, $tip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tips';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tip->setPictureName($newFileName);
            }
            $tip->setTool($tool);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tip);
            $entityManager->flush();

            return $this->redirectToRoute('tip_index', [
                'slug' => $tool->getSlug(),
                'categorySlug' => $tool->getCategory()->getSlug(),
                'themeSlug' => $tool->getCategory()->getTheme()->getSlug()
            ]);
        }

        return $this->render('tip/new.html.twig', [
            'tip' => $tip,
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/back-office/{themeSlug}/{categorySlug}/{slug}/tips/{id}/edit", name="tip_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tip $tip): Response
    {
        $form = $this->createForm(TipType::class, $tip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tips';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($tip->getPictureName());
                $tip->setPictureName($newFileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tip_index', [
                'slug' => $tip->getTool()->getSlug(),
                'categorySlug' => $tip->getTool()->getCategory()->getSlug(),
                'themeSlug' => $tip->getTool()->getCategory()->getTheme()->getSlug()
            ]);
        }

        return $this->render('tip/edit.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/back-office/tips/{id}", name="tip_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tip $tip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tip_index', [
            'slug' => $tip->getTool()->getSlug(),
            'categorySlug' => $tip->getTool()->getCategory()->getSlug(),
            'themeSlug' => $tip->getTool()->getCategory()->getTheme()->getSlug()
        ]);
    }
}
