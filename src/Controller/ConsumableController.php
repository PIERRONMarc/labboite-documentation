<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Entity\Consumable;
use App\Form\ConsumableType;
use App\Form\FinalConsumableType;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ToolRepository;
use App\Repository\ConsumableRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ConsumableController extends AbstractController
{
    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/{slug}/consumable", name="consumable_index", methods={"GET"})
     */
    public function index(ConsumableRepository $consumableRepository, Tool $tool): Response
    {
        return $this->render('consumable/index.html.twig', [
            'consumables' => $consumableRepository->findBy(['tool'=>$tool]),
            'tool' => $tool
        ]);
    }
    
    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/{slug}/consumable/new", name="consumable_new", methods={"GET","POST"})
     */
    public function new(Request $request, Tool $tool): Response
    {
        $consumable = new Consumable();
        $form = $this->createForm(ConsumableType::class, $consumable);
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
                    $destination = $this->getParameter('kernel.project_dir').'/public/upload/consumables';
                    $pictureName->move(
                       $destination,
                        $newFilename
                        
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $consumable->setPictureName($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $consumable->setTool($tool);
            $entityManager->persist($consumable);
            $entityManager->flush();

            return $this->redirectToRoute('consumable_index',[
                'slug'=>$consumable->getTool()->getSlug(),
                'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
                'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
            ]);
        }

        return $this->render('consumable/new.html.twig', [
            'consumable' => $consumable,
            'form' => $form->createView(),
            'tool' => $tool
        ]);
    }

    /**
     * @Route("back-office/{themeSlug}/{categorySlug}/{toolSlug}/consumable/{consumable}/edit", name="consumable_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Consumable $consumable): Response
    {
        $form = $this->createForm(ConsumableType::class, $consumable);
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
                    $destination = $this->getParameter('kernel.project_dir').'/public/img/consumables';
                    $pictureName->move(
                       $destination,
                        $newFilename
                        
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $consumable->setPictureName($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('consumable_index', [
                'slug'=>$consumable->getTool()->getSlug(),
                'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
                'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
            ]);
        }

        return $this->render('consumable/edit.html.twig', [
            'consumable' => $consumable,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("back-office/consumable/{id}", name="consumable_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Consumable $consumable): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consumable->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($consumable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('consumable_index', [
            'slug'=>$consumable->getTool()->getSlug(),
            'themeSlug' => $consumable->getTool()->getCategory()->getTheme()->getSlug(),
            'categorySlug' => $consumable->getTool()->getCategory()->getSlug()
        ]);
    }
}
