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


class InformationController extends AbstractController
{
    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/{slug}/information/edit", name="information_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tool $tool): Response
    {
        $form = $this->createForm(FinalInformationType::class, $tool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureName = $form->get('information')->get('imageFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictureName) {
                $originalFilename = pathinfo($pictureName->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$pictureName->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $destination = $this->getParameter('kernel.project_dir').'/public/upload/informations';
                    $pictureName->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $tool->getInformation()->setPictureName($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tool);
            $entityManager->flush();

            return $this->redirectToRoute('tool_index', ['slug' => $tool->getCategory()->getSlug()] );
        }

        return $this->render('information/edit.html.twig', [
            'tool' => $tool,
            'form' => $form->createView(),
        ]);
    }
}
