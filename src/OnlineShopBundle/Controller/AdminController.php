<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\User;
use OnlineShopBundle\Form\EditUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{

    const NUM_RESULTS = 5;

    /**
     * @Route("/admin/users", name="all_users")
     *
     * @param Request $request
     * @return Response
     */
    public function showUsersAction(Request $request)
    {
        // Is admin
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'You do not have permission');

            return $this->redirectToRoute('all_products');
        }
        $paginator = $this->get('knp_paginator');

        $query = $this->getDoctrine()
            ->getRepository("OnlineShopBundle:User")
            ->createQueryBuilder('p')
            ->select('p');

        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1),
            self::NUM_RESULTS
        );

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy([]);

        return $this->render('admin/users.html.twig',
            [
                'users' => $users,
                "pagination" => $pagination
            ]);
    }

    /**
     * @Route("/user/{id}", name="show_user")
     * @param User $user
     * @return Response
     */
    public function showUserAction(User $user)
    {
        // Is admin
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'You do not have permission');

            return $this->redirectToRoute('all_products');
        }
        return $this->render('admin/user_show.html.twig', array(
            'user' => $user,

        ));

    }

    /**
     * @Route("/user/delete/{id}", name="delete_user_process")
     * @Method("POST")
     * @param User $user
     * @return Response
     */
    public function deleteUser(User $user)
    {
        // Is admin
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'You do not have permission');

            return $this->redirectToRoute('all_products');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash("delete", "The user was successfully deleted");

        return $this->redirectToRoute("all_users");
    }


    /**
     * @Route("/user/edit/{id}", name="edit_user_form")
     * @Method("GET")
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        // Is admin
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'You do not have permission');

            return $this->redirectToRoute('all_products');
        }

        $form = $this->createForm(
            EditUserType::class,
            $user
        );
        return $this->render("admin/user_edit.html.twig",
            ["userForm" => $form->createView()]);
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user_process")
     * @Method("POST")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editProcess(User $user, Request $request)
    {
        // Is admin
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $this->get('session')->getFlashBag()->add('error', 'You do not have permission');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(
            EditUserType::class,
            $user
        );
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()
                ->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("info", "User with username \"" . $user->getUsername() . "\" was edited successfully");

            return $this->redirectToRoute("all_users");
        }
        return $this->render("admin/user_edit.html.twig",
            ["userForm" => $form->createView()]);
    }
}
