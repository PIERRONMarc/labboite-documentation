<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Tool;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\ThemeRepository;
use App\Service\HeaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Question handling - tool page
 */
class QuestionController extends AbstractController
{
    /**
     * Index - front office
     * 
     * @Route("{themeSlug}/{categorySlug}/{slug}/faq", name="question_index", methods={"GET"})
     */
    public function index(QuestionRepository $questionRepository, Tool $tool, ThemeRepository $themeRepository, HeaderHelper $headerHelper): Response
    {
        //get all non empty section of the tool header as an array
        $header = $headerHelper->getToolHeader($tool);
        
        return $this->render('question/public/index.html.twig', [
            'tool' => $tool,
            'questions' => $questionRepository->findBy(['tool' => $tool]),
            'themes' => $themeRepository->findAll(),
            'header' => $header
        ]);
    }

     /**
      * Index - back office
      *
      * @Route("admin/{themeSlug}/{categorySlug}/{slug}/faq", name="admin_question_index", methods={"GET"})
     */
    public function adminIndex(QuestionRepository $questionRepository, Tool $tool, ThemeRepository $themeRepository): Response
    {
        return $this->render('question/admin/index.html.twig', [
            'tool' => $tool,
            'questions' => $questionRepository->findBy(['tool' => $tool]),
            'themes' => $themeRepository->findAll()
        ]);
    }
    /**
     * Creation form question - back office
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/faq/new/", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool, ThemeRepository $themeRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setTool($tool);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('admin_question_index', [
                'slug' => $tool->getSlug(),
                'categorySlug' => $tool->getCategory()->getSlug(),
                'themeSlug' => $tool->getCategory()->getTheme()->getSlug(),
            ]);
        }

        return $this->render('question/admin/new.html.twig', [
            'tool' => $tool,
            'question' => $question,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
        ]);
    }
   
    /**
     * Edition form - back office
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{toolSlug}/faq/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_question_index', [
                'slug' => $question->getTool()->getSlug(),
                'categorySlug' => $question->getTool()->getCategory()->getSlug(),
                'themeSlug' => $question->getTool()->getCategory()->getTheme()->getSlug(),
            ]);
        }

        return $this->render('question/admin/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'tool' => $question->getTool()
        ]);
    }

    /**
     * Delete a question - back office
     * 
     * @Route("admin/faq/{id}", name="question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Question $question): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_question_index', [
            'slug' => $question->getTool()->getSlug(),
            'categorySlug' => $question->getTool()->getCategory()->getSlug(),
            'themeSlug' => $question->getTool()->getCategory()->getTheme()->getSlug(),
        ]);
    }
}
