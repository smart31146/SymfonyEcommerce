<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Promotion;
use OnlineShopBundle\Form\PromotionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends Controller
{
    /**
     * @Route("/promotion/add", name="add_promotion_form")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPromotion()
    {
        // Is authenticated to add promotion
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(PromotionType::class);
        return $this->render('promotion/add.html.twig', ['promotionForm' => $form->createView()]);
    }

    /**
     * @Route("/promotion/add", name="add_promotion_process")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPromotionProcess(Request $request)
    {
        // Is authenticated to add promotion
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($promotion);
            $entityManager->flush();
            return $this->redirectToRoute('all_products');
        }

        return $this->render('promotion/add.html.twig', ['promotionForm' => $form->createView()]);
    }

}
