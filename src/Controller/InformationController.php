<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Form\FinalInformationType;
use App\Repository\ToolRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/information")
 */
class InformationController extends AbstractController
{
    /**
     * @Route("/", name="information_index")
     */
    public function index(ToolRepository $toolRepository)
    {
        return $this->render('information/index.html.twig', [
            'tool' => $toolRepository->findAll()
        ]);
    }
    
    /**
     * @Route("/edit/{name}", name="information_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool): Response
    {
        $form = $this->createForm(FinalInformationType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picturePath = $form->get('information')->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($picturePath) {
                $originalFilename = pathinfo($picturePath->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$picturePath->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/informations';
                    $picturePath->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $tool->getInformation()->setPicturePath($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

            return $this->redirectToRoute('information_index');
        }

        return $this->render('information/edit.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{name}", name="information_show", methods={"GET"})
     */
    public function show(Tool $tool): Response
    {
        return $this->render('information/show.html.twig', [
            'tool' => $tool,
        ]);
    }
}
