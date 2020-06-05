<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Service\FileUploader;
use App\Form\FinalInformationType;
use App\Repository\ThemeRepository;
use App\Service\HeaderHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Information handling - tool page
 */
class InformationController extends AbstractController
{
    /**
     * Index and form - back office
     * 
     * Handle new information object and edition at the same time.
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/information", name="admin_information_index", methods={"GET","POST"})
     */
    public function adminIndex(Request $request, Tool $tool, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(FinalInformationType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('information')->get('imageFile')->getData();

            // if image is filled, upload it at the correct path
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/informations';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($tool->getInformation()->getPictureName());
                $tool->getInformation()->setPictureName($newFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

        }

        return $this->render('information/admin/index.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $tool->getCategory()->getTheme()
        ]);
    }

    /**
     * Index - front office
     * 
     * @Route("{themeSlug}/{categorySlug}/{slug}/information", name="information_index", methods={"GET"})
     */
    public function index(Tool $tool, ThemeRepository $themeRepository, HeaderHelper $headerHelper)
    {
        //get all non empty section of the tool header as an array
        $header = $headerHelper->getToolHeader($tool);
        
        return $this->render('information/public/index.html.twig', [
            'tool' => $tool,
            'themes' => $themeRepository->findAll(),
            'header' => $header
        ]);
    }
}
