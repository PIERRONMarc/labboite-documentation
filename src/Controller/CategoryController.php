<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Theme;
use App\Form\CategoryType;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\CategoryRepository;
use App\Repository\ThemeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{name}", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, Theme $theme): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy(['theme' => $theme]),
            'theme' => $theme
        ]);
    }

    /**
     * @Route("/new/{name}", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request, Theme $theme): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picturePath = $form->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($picturePath) {
                $originalFilename = pathinfo($picturePath->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$picturePath->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/categorys';
                    $picturePath->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $category->setThumbnailPath($newFilename);
            }
            $category->setTheme($theme);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', ['name' => $theme->getName()]);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picturePath = $form->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($picturePath) {
                $originalFilename = pathinfo($picturePath->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$picturePath->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/categorys';
                    $picturePath->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $category->setThumbnailPath($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index', ['name' => $category->getTheme()->getName()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index', ['name' => $category->getTheme()->getName()]);
    }
}
