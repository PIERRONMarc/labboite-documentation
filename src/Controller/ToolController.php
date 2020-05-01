<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Tool;
use App\Form\ToolType;
use App\Repository\CategoryRepository;
use App\Repository\ToolRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tool")
 */
class ToolController extends AbstractController
{
    /**
     * @Route("/category", name="tool_category", methods={"GET"})
     */
    public function categories(CategoryRepository $categoryRepository): Response
    {
        return $this->render('tool/categories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{name}", name="tool_new", methods={"GET","POST"})
     */
    public function new(Request $request, Category $category): Response
    {
        
        $tool = new Tool();
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // file input handling
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/img/tools';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tool->setPicturePath($newFileName);
            }

            $tool->setCategory($category);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

            return $this->redirectToRoute('tool_index', ['name' => $category->getName()]);
        }

        return $this->render('tool/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{name}/show", name="tool_show", methods={"GET"})
     */
    public function show(Tool $tool): Response
    {
        return $this->render('tool/show.html.twig', [
            'tool' => $tool,
        ]);
    }

    /**
     * @Route("/{name}/edit", name="tool_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool): Response
    {
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // file input handling
            $uploadedFile = $form['imageFile']->getData();
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/img/tools';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $tool->setPicturePath($newFileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tool_index', ['name' => $tool->getCategory()->getName()]);
        }

        return $this->render('tool/edit.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{name}", name="tool_index")
     */
    public function index(ToolRepository $toolRepository, Category $category): Response
    {
        return $this->render('tool/index.html.twig', [
            'tools' => $toolRepository->findBy(['category' => $category]),
            'category' => $category
        ]);
    }

    /**
     * @Route("/{id}", name="tool_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tool $tool): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tool->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tool);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tool_index', ['name' => $tool->getCategory()->getName()]);
    }
}
