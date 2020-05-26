<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\FileUploader;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

class CategoryController extends AbstractController
{
    /**
     * @Route("/{slug}/category", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, ThemeRepository $themeRepository, Theme $theme): Response
    {
        return $this->render('category/public/index.html.twig', [
            'categories' => $categoryRepository->findBy(['theme' => $theme]),
            'currentTheme' => $theme,
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $theme
        ]);
    }

    /**
     * @Route("admin/{slug}/category", name="admin_category_index", methods={"GET"})
     */
    public function adminIndex(CategoryRepository $categoryRepository, ThemeRepository $themeRepository, Theme $theme): Response
    {
        return $this->render('category/admin/index.html.twig', [
            'categories' => $categoryRepository->findBy(['theme' => $theme]),
            'currentTheme' => $theme,
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $theme
        ]);
    }

    /**
     * @Route("admin/{slug}/category/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request, Theme $theme, ThemeRepository $themeRepository): Response
    {
        $category = new Category();
        $category->setTheme($theme);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/category';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $category->setThumbnailName($newFileName);
            }

            $category->setSlug(Urlizer::urlize($category->getName()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_category_index', ['slug' => $theme->getSlug()]);
        }

        return $this->render('category/admin/new.html.twig', [
            'category' => $category,
            'theme' => $theme,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $theme
        ]);
    }

    /**
     * @Route("admin/{themeSlug}/{categorySlug}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $themeSlug, $categorySlug, CategoryRepository $categoryRepo, ThemeRepository $themeRepository): Response
    {
        $category = $categoryRepo->findBySlugs($categorySlug, $themeSlug);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($uploadedFile) {
                $destination = $this->getParameter('kernel.project_dir').'/public/upload/category';
                $fileUploader = new FileUploader($destination);
                $newFileName = $fileUploader->upload($uploadedFile);
                $fileUploader->deleteFile($category->getThumbnailName());
                $category->setThumbnailName($newFileName);
            }
            $category->setSlug(Urlizer::urlize($category->getName()));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_category_index', ['slug' => $category->getTheme()->getSlug()]);
        } 

        if ($category->getThumbnailName()) {
            $imgRelativeUrl = 'upload/category/' . $category->getThumbnailName();
        } else {
            $imgRelativeUrl = null;
        }
        return $this->render('category/admin/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'themes' => $themeRepository->findAll(),
            'actualTheme' => $category->getTheme(),
            'imgRelativeUrl' => $imgRelativeUrl
        ]);
    }

    /**
     * @Route("admin/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_category_index', ['slug' => $category->getTheme()->getSlug()]);
    }
}
