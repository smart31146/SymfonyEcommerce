<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Product;
use OnlineShopBundle\Entity\Tag;
use OnlineShopBundle\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{
    const NUM_RESULTS = 5;

    /**
     * @Route("/products/{id}/tags/add", name="add_tag_form")
     * @Method("GET")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addTag(Product $product)
    {
        // Is authenticated to add tag
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(TagType::class);
        return $this->render('tags/add.html.twig', ['tagForm' => $form->createView()]);
    }

    /**
     * @Route("/products/{id}/tags/add", name="add_tag_process")
     * @Method("POST")
     *
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addTagProcess(Product $product, Request $request)
    {
        // Is authenticated to add tag
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $repository = $this->getDoctrine()->getRepository(Tag::class);
            $tagDb = $repository->findOneBy(['name' => $tag->getName()]);
            if ($tagDb) {
                $tag = $tagDb;
            }

            $tag->getProducts()->add($product);
            $product->getTags()->add($tag);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tag);
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('all_products');
        }

        return $this->render('tags/add.html.twig', ['tagForm' => $form->createView()]);
    }

//    /**
//     * @Route("/products/{id}/tags", name="tags_by_product")
//     * @param Product $product
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function viewTagsByProduct(Product $product)
//    {
//        return $this->render("tags/product.html.twig", ["product" => $product]);
//    }

    /**
     * @Route("/tags/{id}", name="tag_products")
     * @param Tag $tag
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProductsByTag(Tag $tag, Request $request)
    {
        $paginator = $this->get('knp_paginator');

        $query = $this->getDoctrine()
            ->getRepository("OnlineShopBundle:Product")
            ->createQueryBuilder('p')
            ->select('p');

        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1),
            self::NUM_RESULTS
        );
        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();
        return $this->render("tags/one.html.twig",
            [
                "tag" => $tag,
                "max_promotion" => $max_promotion,
                "calc" => $calc,
                "pagination" => $pagination
            ]);

    }
}
