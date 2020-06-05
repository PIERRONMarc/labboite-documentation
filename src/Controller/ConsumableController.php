<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Entity\Consumable;
use App\Form\ConsumableType;
use App\Service\FileUploader;
use App\Repository\ConsumableRepository;
use App\Repository\ThemeRepository;
use App\Service\HeaderHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Consumables handling - tool page
 */
class ConsumableController extends AbstractController
{
    /**
     * Index - front office
     * 
     * @Route("{themeSlug}/{categorySlug}/{slug}/consumable", name="consumable_index", methods={"GET"})
     */
    public function index(ConsumableRepository $consumableRepository, Tool $tool, ThemeRepository $themeRepository, HeaderHelper $headerHelper): Response
    {
        //get all non empty section of the tool header as an array
        $header = $headerHelper->getToolHeader($tool);
        
        return $this->render('consumable/public/index.html.twig', [
            'consumables' => $consumableRepository->findBy(['tool'=>$tool]),
            'tool' => $tool,
            'themes' => $themeRepository->findAll(),
            'header' => $header
        ]);
    }
    
    /**
     * Index - back office 
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/consumable", name="admin_consumable_index", methods={"GET"})
     */
    public function adminIndex(ConsumableRepository $consumableRepository, Tool $tool, ThemeRepository $themeRepository): Response
    {
        return $this->render('consumable/admin/index.html.twig', [
            'consumables' => $consumableRepository->findBy(['tool'=>$tool]),
            'tool' => $tool,
            'themes' => $themeRepository->findAll()
        ]);
    }
    
    /**
     * Creation form - back office
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{slug}/consumable/new", name="consumable_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool, ThemeRepository $themeRepository): Response
    {
        $consumable = new Consumable();
        $form = $this->createForm(ConsumableType::class, $consumable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $uploadedFile = $form->get('imageFile')->getData();
            
            // if image is filled, upload it at the correct path
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/consumable';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $consumable->setPictureName($newFileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $consumable->setTool($tool);
            $entityManager->persist($consumable);
            $entityManager->flush();

            return $this->redirectToRoute('admin_consumable_index',[
                'slug'=>$consumable->getTool()->getSlug(),
                'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
                'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
            ]);
        }

        return $this->render('consumable/admin/new.html.twig', [
            'consumable' => $consumable,
            'form' => $form->createView(),
            'tool' => $tool,
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $tool->getCategory()->getTheme()
        ]);
    }

    /**
     * Edition form - back office
     * 
     * @Route("admin/{themeSlug}/{categorySlug}/{toolSlug}/consumable/{consumable}/edit", name="consumable_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Consumable $consumable, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(ConsumableType::class, $consumable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();
            
            // if image is filled, upload it at the correct path
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/consumable';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($consumable->getPictureName());
                $consumable->setPictureName($newFileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_consumable_index', [
                'slug'=>$consumable->getTool()->getSlug(),
                'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
                'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
            ]);
        }

        return $this->render('consumable/admin/edit.html.twig', [
            'consumable' => $consumable,
            'form' => $form->createView(),
            'themes' =>$themeRepository->findAll(),
            'actualTheme' => $consumable->getTool()->getCategory()->getTheme(),
            'tool' => $consumable->getTool()
        ]);
    }

    /**
     * Delete a consumable - back office
     * 
     * @Route("admin/consumable/{id}", name="consumable_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Consumable $consumable): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consumable->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($consumable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_consumable_index', [
            'slug'=>$consumable->getTool()->getSlug(),
            'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
            'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
        ]);
    }
}
