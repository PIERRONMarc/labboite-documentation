<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserEditionFormType;
use App\Form\RegistrationFormType;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * User handling - back office
 * 
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * List all users
     * 
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, ThemeRepository $themeRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'themes' => $themeRepository->findAll(),
        ]);
    }

    /**
     * Creation form
     * 
     * @Route("/new", name="user_new")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, ThemeRepository $themeRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $user->plainPassword
                )
            );

            $roles = ['ROLE_ADMIN'];
            if ($form->get('superAdmin')->getData()) {
                array_push($roles, 'ROLE_SUPER-ADMIN');
            }
            $user->setRoles($roles);
            $user->setRegistrationDate(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/registration.html.twig', [
            'registrationForm' => $form->createView(),
            'themes' => $themeRepository->findAll(),
        ]);
    }

    /**
     * Edition form
     * 
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, ThemeRepository $themeRepository): Response
    {
        if (in_array('ROLE_SUPER-ADMIN', $user->getRoles())) {
            $user->setIsSuperAdmin(true);
        }

        $form = $this->createForm(UserEditionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $roles = ['ROLE_ADMIN'];
            if ($form->get('isSuperAdmin')->getData()) {
                array_push($roles, 'ROLE_SUPER-ADMIN');
            }
            $user->setRoles($roles);
            $user->setRegistrationDate(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $form->createView(),
            'themes' => $themeRepository->findAll(),
        ]);
    }

    /**
     * Password change form
     * 
     * @Route("/{id}/change-password", name="user_change_password", methods={"GET", "POST"})
     */
    public function changePassword(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, ThemeRepository $themeRepository) {

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordEncoder->isPasswordValid($user, $user->oldPassword)) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $user->plainPassword
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // do anything else you need here, like send an email
                return $this->redirectToRoute('user_index');

            } else {
                $form->get('oldPassword')->addError(new FormError('Old password is incorrect'));
            }
        }

        return $this->render('user/changePassword.html.twig', [
            'passwordForm' => $form->createView(),
            'user' => $user,
            'themes' => $themeRepository->findAll(),
        ]);
    }

    /**
     * Delete a user
     * 
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        // prevent the user to delete his own account
        if ($user == $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte');
        } else {
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('user_index');
    }

    
}
