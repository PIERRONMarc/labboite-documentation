<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Form\ToolType;
use App\Entity\Category;
use App\Repository\ThemeRepository;
use App\Service\FileUploader;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Tool handling
 */
class ToolController extends AbstractController
{
    /**
     * Index - front office
     * 
     * @Route("{themeSlug}/{slug}/tool", name="tool_index")
     */
    public function index(ToolRepository $toolRepository, Category $category, ThemeRepository $themeRepository): Response
    {
        return $this->render('tool/public/index.html.twig', [
            'tools' => $toolRepository->findBy(['category' => $category], ['displayOrder' => 'ASC']),
            'themes' => $themeRepository->findAll(),
            'category' => $category
        ]);
    }

    /**
     * Index - back office
     * 
     * @Route("admin/{themeSlug}/{slug}/tool", name="admin_tool_index")
     */
    public function adminIndex(ToolRepository $toolRepository, Category $category, ThemeRepository $themeRepository): Response
    {
        return $this->render('tool/admin/index.html.twig', [
            'tools' => $toolRepository->findBy(['category' => $category], ['displayOrder' => 'ASC']),
            'themes' => $themeRepository->findAll(),
            'category' => $category,
            'actualTheme' => $category->getTheme()
        ]);
    }
    
    /**
     * Creation form - back office
     * 
     * @Route("admin/{themeSlug}/{slug}/tool/new", name="tool_new", methods={"GET","POST"})
     */
    public function new(Request $request, Category $category, ValidatorInterface $validator, ThemeRepository $themeRepository): Response
    {
        
        $tool = new Tool();
        $tool->setCategory($category);
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['imageFile']->getData();
            // if image is filled, upload it at the correct path
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tool';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tool->setPictureName($newFileName);
            }

            $tool->setSlug(Urlizer::urlize($tool->getName()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

            return $this->redirectToRoute('admin_tool_index', ['slug' => $category->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
        }

        return $this->render('tool/admin/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $category->getTheme()
        ]);
    }

    /**
     * Edition form - back office
     * 
     * @Route("admin/{themeSlug}/{slug}/tool/edit", name="tool_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool, ThemeRepository $themeRepository): Response
    {
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // if image is filled, upload it at the correct path
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tool';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($tool->getPictureName());
                $tool->setPictureName($newFileName);
            }
            $tool->setSlug(Urlizer::urlize($tool->getName()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_tool_index', ['slug' => $tool->getCategory()->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
        }

        return $this->render('tool/admin/edit.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $tool->getCategory()->getTheme(),
        ]);
    }

    /**
     * Delete a tool - back office
     * 
     * @Route("admin/tool/{id}", name="tool_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tool $tool): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tool->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tool);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_tool_index', ['slug' => $tool->getCategory()->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
    }
}
