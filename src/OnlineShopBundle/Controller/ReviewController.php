<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Product;
use OnlineShopBundle\Entity\Review;
use OnlineShopBundle\Form\ReviewType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReviewController extends Controller
{
    /**
     * @Route("/products/{id}/reviews", name="product_reviews")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewByProduct(Product $product)
    {
        return $this->render('reviews/product.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/product/{id}/reviews/add", name="leave_review_form")
     * @Method("GET")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function leaveReviewFormAction(Product $product)
    {
        $form = $this->createForm(
            ReviewType::class
        );
        return $this->render("reviews/leave_review.html.twig", [
            "reviewForm" => $form->createView(),
            "product" => $product
        ]);
    }

    /**
     * @Route("/product/{id}/reviews/add", name="leave_review_process")
     * @Method("POST")
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function leaveReviewProcess(Product $product, Request $request)
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $review->setProduct($product);
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();
            $this->addFlash('info', "Review added!");
            return $this->redirectToRoute("product_reviews", ['id' => $product->getId()]);
        }
        return $this->render("reviews/product.html.twig",
            [
                'product' => $product
            ]);
    }
}
