<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ThemeRepository $themeRepository)
    {
        if ($themeRepository->findFirstRecord()) {
            return $this->redirectToRoute('category_index', ['slug' => $themeRepository->findFirstRecord()->getSlug()]);
        } else {
            return $this->render('main/empty.html.twig');
        }
    }
}
