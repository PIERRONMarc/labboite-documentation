<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Root of the project
 */
class MainController extends AbstractController
{
    /**
     * Root
     * 
     * Handle which route to go next
     * 
     * @Route("/", name="home")
     */
    public function index(ThemeRepository $themeRepository)
    {
        $firstTheme = $themeRepository->findFirstRecord();

        // if database is not empty
        if ($firstTheme) {
            // if user is logged we redirect to back office
            if ($this->getUser()) {
                return $this->redirectToRoute('admin_category_index', ['slug' => $firstTheme->getSlug()]);
            } else {
                return $this->redirectToRoute('category_index', ['slug' => $firstTheme->getSlug()]);
            }
        } else {
            return $this->render('main/empty.html.twig');
        }
    }
}
