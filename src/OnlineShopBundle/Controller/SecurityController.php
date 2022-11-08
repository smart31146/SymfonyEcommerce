<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Form\SecurityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="our_login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login()
    {
        $form = $this->createForm(SecurityType::class);

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            "loginForm" => $form->createView()
        ));
    }

    /**
     * @Route("/logout", name="logout")
     *
     */
    public function logout()
    {

    }
}
