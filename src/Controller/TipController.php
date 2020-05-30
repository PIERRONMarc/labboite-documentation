<?php

namespace App\Controller;

use App\Entity\Tip;
use App\Entity\Tool;
use App\Form\TipType;
use App\Repository\ThemeRepository;
use App\Service\FileUploader;
use App\Repository\TipRepository;
use App\Service\HeaderHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TipController extends AbstractController
{
    /**
     * @Route("{themeSlug}/{categorySlug}/{slug}/tips", name="tip_index", methods={"GET"})
     */
    public function index(TipRepository $tipRepository, Tool $tool, ThemeRepository $themeRepository, HeaderHelper $headerHelper): Response
    {
        $header = $headerHelper->getToolHeader($tool);
        
        return $this->render('tip/public/index.html.twig', [
            'tips' => $tipRepository->findBy(['tool' => $tool]),
            'tool' => $tool,
            'themes' => $themeRepository->findAll(),
            'header' => $header
        ]);
    }


      /**
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/tips", name="admin_tip_index", methods={"GET"})
     */
    public function adminIndex(TipRepository $tipRepository, Tool $tool, ThemeRepository $themeRepository): Response
    {
        return $this->render('tip/admin/index.html.twig', [
            'tips' => $tipRepository->findBy(['tool' => $tool]),
            'tool' => $tool,
            'themes' => $themeRepository->findAll()
        ]);
    }

    /**
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/tips/new", name="tip_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool, ThemeRepository $themeRepository, ValidatorInterface $validator): Response
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

            if ($tip->getPictureName() && $tip->getYoutubeLink()) {
                $violation = true;
                $this->addFlash('danger', 'Vous ne pouvez pas insérer une image et une vidéo en même temps !');
            }

            if (!isset($violation)) {
                $tip->setTool($tool);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tip);
                $entityManager->flush();

                return $this->redirectToRoute('admin_tip_index', [
                    'slug' => $tool->getSlug(),
                    'categorySlug' => $tool->getCategory()->getSlug(),
                    'themeSlug' => $tool->getCategory()->getTheme()->getSlug()
                ]);
            } 

        }

        return $this->render('tip/admin/new.html.twig', [
            'tip' => $tip,
            'tool' => $tool,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $tool->getCategory()->getTheme(),
        ]);
    }

    /**
     * @Route("/admin/{themeSlug}/{categorySlug}/{slug}/tips/{id}/edit", name="tip_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tip $tip, ThemeRepository $themeRepository, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(TipType::class, $tip);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('youtubeLink')->getData()) {
                $tip->setPictureName(null);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            if ($tip->getPictureName() && $tip->getYoutubeLink()) {
                $tip->setPictureName(null);
            }

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tips';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($tip->getPictureName());
                $tip->setPictureName($newFileName);
            }

            if ($uploadedFile && $tip->getYoutubeLink()) {
                $violation = true;
                $this->addFlash('danger', 'Vous ne pouvez pas insérer une image et une vidéo en même temps !');
                $tip->setPictureName(null);
            }

            if (!isset($violation)) { 
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('admin_tip_index', [
                    'slug' => $tip->getTool()->getSlug(),
                    'categorySlug' => $tip->getTool()->getCategory()->getSlug(),
                    'themeSlug' => $tip->getTool()->getCategory()->getTheme()->getSlug()
                ]);
            }
        }

        return $this->render('tip/admin/edit.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
            'themes' =>$themeRepository->findAll(),
            'actualTheme' => $tip->getTool()->getCategory()->getTheme(),
            'tool' => $tip->getTool(),
        ]);
    }

    /**
     * @Route("/admin/tips/{id}", name="tip_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tip $tip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_tip_index', [
            'slug' => $tip->getTool()->getSlug(),
            'categorySlug' => $tip->getTool()->getCategory()->getSlug(),
            'themeSlug' => $tip->getTool()->getCategory()->getTheme()->getSlug()
        ]);
    }
}
