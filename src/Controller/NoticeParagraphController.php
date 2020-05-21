<?php

namespace App\Controller;

use App\Entity\NoticeParagraph;
use App\Entity\Tool;
use App\Form\NoticeParagraphType;
use App\Repository\NoticeParagraphRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoticeParagraphController extends AbstractController
{
    /**
     * @Route("{themeSlug}/{categorySlug}/{slug}/notice", name="notice_paragraph_index", methods={"GET"})
     */
    public function index(NoticeParagraphRepository $noticeParagraphRepository, Tool $tool): Response
    {
        return $this->render('notice_paragraph/index.html.twig', [
            'tool' => $tool,
            'notice_paragraphs' => $noticeParagraphRepository->findBy(['tool' => $tool]),
        ]);
    }
    
    /**
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/notice/new", name="notice_paragraph_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool): Response
    {
        $noticeParagraph = new NoticeParagraph();
        $form = $this->createForm(NoticeParagraphType::class, $noticeParagraph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noticeParagraph->setTool($tool);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($noticeParagraph);
            $entityManager->flush();

            return $this->redirectToRoute('tool_index', [
                'slug' => $tool->getCategory()->getSlug(),
                'themeSlug' => $tool->getCategory()->getTheme()->getSlug()
            ]);
        }

        return $this->render('notice_paragraph/new.html.twig', [
            'notice_paragraph' => $noticeParagraph,
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/{themeSlug}/{categorySlug}/{toolSlug}/notice/{id}/edit", name="notice_paragraph_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, NoticeParagraph $noticeParagraph): Response
    {
        $form = $this->createForm(NoticeParagraphType::class, $noticeParagraph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notice_paragraph_index', [
                'slug' => $noticeParagraph->getTool()->getSlug(),
                'themeSlug' => $noticeParagraph->getTool()->getCategory()->getTheme()->getSlug(),
                'categorySlug' => $noticeParagraph->getTool()->getCategory()->getSlug()
            ]);
        }

        return $this->render('notice_paragraph/edit.html.twig', [
            'notice_paragraph' => $noticeParagraph,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/notice/{id}", name="notice_paragraph_delete", methods={"DELETE"})
     */
    public function delete(Request $request, NoticeParagraph $noticeParagraph): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noticeParagraph->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($noticeParagraph);
            $entityManager->flush();
        }

        return $this->redirectToRoute('notice_paragraph_index', [
            'slug' => $noticeParagraph->getTool()->getCategory()->getSlug(),
            'themeSlug' => $noticeParagraph->getTool()->getCategory()->getTheme()->getSlug(),
            'categorySlug' => $noticeParagraph->getTool()->getCategory()->getSlug(),
        ]);
    }
}
