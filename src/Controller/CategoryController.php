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

class CategoryController extends AbstractController
{
    /**
     * @Route("/{slug}/category", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, ThemeRepository $themeRepository, Theme $theme): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findBy(['theme' => $theme]),
            'currentTheme' => $theme,
            'themes' => $themeRepository->findAll()
        ]);
    }

    /**
     * @Route("back-office/{slug}/category/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request, Theme $theme): Response
    {
        $category = new Category();
        $category->setTheme($theme);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureName = $form->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureName) {
                $originalFilename = pathinfo($pictureName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$pictureName->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/upload/category';
                    $pictureName->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $category->setThumbnailName($newFilename);
            }

            $category->setSlug(Urlizer::urlize($category->getName()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', ['slug' => $theme->getSlug()]);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'theme' => $theme,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $themeSlug, $categorySlug, CategoryRepository $categoryRepo): Response
    {
        $category = $categoryRepo->findBySlugs($categorySlug, $themeSlug);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureName = $form->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureName) {
                $originalFilename = pathinfo($pictureName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$pictureName->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/upload/category';
                    $pictureName->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $category->setThumbnailName($newFilename);
            }
            $category->setSlug(Urlizer::urlize($category->getName()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index', ['slug' => $category->getTheme()->getSlug()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("back-office/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index', ['slug' => $category->getTheme()->getSlug()]);
    }
}
