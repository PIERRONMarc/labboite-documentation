<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Form\ToolType;
use App\Entity\Category;
use App\Service\FileUploader;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ToolController extends AbstractController
{
    /**
     * @Route("{themeSlug}/{slug}/tool", name="tool_index")
     */
    public function index(ToolRepository $toolRepository, Category $category): Response
    {
        return $this->render('tool/index.html.twig', [
            'tools' => $toolRepository->findBy(['category' => $category]),
            'category' => $category
        ]);
    }
    
    /**
     * @Route("back-office/{themeSlug}/{slug}/tool/new", name="tool_new", methods={"GET","POST"})
     */
    public function new(Request $request, Category $category, ValidatorInterface $validator): Response
    {
        
        $tool = new Tool();
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // file input handling
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tool';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tool->setPictureName($newFileName);
            }

            $tool->setSlug(Urlizer::urlize($tool->getName()));
            $tool->setCategory($category);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

            return $this->redirectToRoute('tool_index', ['slug' => $category->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
        }

        return $this->render('tool/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("back-office/{themeSlug}/{slug}/tool/edit", name="tool_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool): Response
    {
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // file input handling
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/tool';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tool->setPictureName($newFileName);
            }
            $tool->setSlug(Urlizer::urlize($tool->getName()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tool_index', ['slug' => $tool->getCategory()->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
        }

        return $this->render('tool/edit.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("back-office/tool/{id}", name="tool_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tool $tool): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tool->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tool);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tool_index', ['slug' => $tool->getCategory()->getSlug(), 'themeSlug' => $tool->getCategory()->getTheme()->getSlug()]);
    }
}
