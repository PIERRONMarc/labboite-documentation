<?php

namespace App\Controller;

use App\Entity\Tip;
use App\Form\TipType;
use App\Repository\TipRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/tip")
 */
class TipController extends AbstractController
{
    /**
     * @Route("/", name="tip_index", methods={"GET"})
     */
    public function index(TipRepository $tipRepository): Response
    {
        return $this->render('tip/index.html.twig', [
            'tips' => $tipRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="tip_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tip = new Tip();
        $form = $this->createForm(TipType::class, $tip);
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
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/tips';
                    $picturePath->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $tip->setPictureName($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tip);
            $entityManager->flush();

            return $this->redirectToRoute('tip_index');
        }

        return $this->render('tip/new.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tip_show", methods={"GET"})
     */
    public function show(Tip $tip): Response
    {
        return $this->render('tip/show.html.twig', [
            'tip' => $tip,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tip_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tip $tip): Response
    {
        $form = $this->createForm(TipType::class, $tip);
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
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/tips';
                    $picturePath->move(
                       $destination,
                        $newFilename

                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $tip->setPictureName($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tip_index');
        }

        return $this->render('tip/edit.html.twig', [
            'tip' => $tip,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tip_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tip $tip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tip_index');
    }
}
