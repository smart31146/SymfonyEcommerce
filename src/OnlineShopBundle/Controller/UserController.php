<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Role;
use OnlineShopBundle\Entity\User;
use OnlineShopBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register_form")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register()
    {
        $form = $this->createForm(UserType::class);
        return $this->render("users/register.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/register", name="user_register_process")
     * @Method("POST")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $encoder = $this->get("security.password_encoder");

        if ($form->isValid()) {
            $hashPassword = $encoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $userRole = $this->getDoctrine()->getRepository(Role::class)
                ->findOneBy(["name" => 'ROLE_USER']);
            $user->addRole($userRole);
            $user->setPassword($hashPassword);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("our_login");
        }
        return $this->render("users/register.html.twig", ["form" => $form->createView()]);
    }

}
