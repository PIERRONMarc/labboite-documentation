<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Entity\Tool;
use App\Form\NoticeType;
use App\Repository\NoticeRepository;
use App\Repository\ThemeRepository;
use App\Service\HeaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handle notice - tool page
 */
class NoticeController extends AbstractController
{
    /**
     * Index - front office
     * 
     * @Route("{themeSlug}/{categorySlug}/{slug}/notice", name="notice_index", methods={"GET"})
     */
    public function index(NoticeRepository $noticeRepository, ThemeRepository $themeRepository, Tool $tool, HeaderHelper $headerHelper): Response
    {
        //get all non empty section of the tool header as an array
        $header = $headerHelper->getToolHeader($tool);
        
        return $this->render('notice/public/index.html.twig', [
            'notices' => $noticeRepository->findAll(),
            'themes' => $themeRepository->findAll(),
            'tool' => $tool,
            'header' => $header
        ]);
    }

    /**
     * Index and form - back office
     * 
     * Handle new notice object and edition at the same time.
     * 
     * @Route("/admin/{themeSlug}/{categorySlug}/{slug}/notice", name="admin_notice_index", methods={"GET","POST"})
     */
    public function adminIndex(Request $request, Tool $tool, ThemeRepository $themeRepository): Response
    {
        // if there is no notice we create one
        if ($tool->getNotice()) {
            $notice = $tool->getNotice();
        } else {
            $notice = new Notice();
        }
        
        $form = $this->createForm(NoticeType::class, $notice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->getContent()) {
                $notice->setTool($tool);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($notice);
                $entityManager->flush();
            }
        }

        return $this->render('notice/admin/edit.html.twig', [
            'notice' => $notice,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'tool' => $tool
        ]);
    }
}
