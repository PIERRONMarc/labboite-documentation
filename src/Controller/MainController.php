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
        return $this->redirectToRoute('category_index', ['name' => $themeRepository->findFirstRecord()]);
    }
}
