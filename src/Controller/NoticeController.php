<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Entity\Tool;
use App\Form\NoticeType;
use App\Repository\NoticeRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoticeController extends AbstractController
{
    /**
     * @Route("{themeSlug}/{categorySlug}/{slug}/notice", name="notice_index", methods={"GET"})
     */
    public function index(NoticeRepository $noticeRepository, ThemeRepository $themeRepository, Tool $tool): Response
    {
        return $this->render('notice/index.html.twig', [
            'notices' => $noticeRepository->findAll(),
            'themes' => $themeRepository->findAll(),
            'tool' => $tool
        ]);
    }

    /**
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/notice/edit", name="notice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool): Response
    {
        if ($tool->getNotice()) {
            $notice = $tool->getNotice();
        } else {
            $notice = new Notice();
        }
        
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notice->setTool($tool);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notice);
            $entityManager->flush();

            return $this->redirectToRoute('tool_index', [
                'slug' => $tool->getCategory()->getSlug(),
                'themeSlug' => $tool->getCategory()->getTheme()->getSlug()
            ]);
        }

        return $this->render('notice/new.html.twig', [
            'notice' => $notice,
            'form' => $form->createView(),
        ]);
    }
}
