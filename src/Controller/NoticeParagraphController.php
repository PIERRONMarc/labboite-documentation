<?php

namespace App\Controller;

use App\Entity\NoticeParagraph;
use App\Form\NoticeParagraphType;
use App\Repository\NoticeParagraphRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notice/paragraph")
 */
class NoticeParagraphController extends AbstractController
{
    /**
     * @Route("/", name="notice_paragraph_index", methods={"GET"})
     */
    public function index(NoticeParagraphRepository $noticeParagraphRepository): Response
    {
        return $this->render('notice_paragraph/index.html.twig', [
            'notice_paragraphs' => $noticeParagraphRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="notice_paragraph_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $noticeParagraph = new NoticeParagraph();
        $form = $this->createForm(NoticeParagraphType::class, $noticeParagraph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($noticeParagraph);
            $entityManager->flush();

            return $this->redirectToRoute('notice_paragraph_index');
        }

        return $this->render('notice_paragraph/new.html.twig', [
            'notice_paragraph' => $noticeParagraph,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notice_paragraph_show", methods={"GET"})
     */
    public function show(NoticeParagraph $noticeParagraph): Response
    {
        return $this->render('notice_paragraph/show.html.twig', [
            'notice_paragraph' => $noticeParagraph,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="notice_paragraph_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, NoticeParagraph $noticeParagraph): Response
    {
        $form = $this->createForm(NoticeParagraphType::class, $noticeParagraph);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notice_paragraph_index');
        }

        return $this->render('notice_paragraph/edit.html.twig', [
            'notice_paragraph' => $noticeParagraph,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notice_paragraph_delete", methods={"DELETE"})
     */
    public function delete(Request $request, NoticeParagraph $noticeParagraph): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noticeParagraph->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($noticeParagraph);
            $entityManager->flush();
        }

        return $this->redirectToRoute('notice_paragraph_index');
    }
}
