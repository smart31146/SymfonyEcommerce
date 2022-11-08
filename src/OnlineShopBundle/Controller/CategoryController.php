<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Category;
use OnlineShopBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    const NUM_RESULTS = 5;

    /**
     * @Route("/category/add", name="add_category_form")
     * @Method("GET")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategory()
    {
        // Is authenticated to add category
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(CategoryType::class);
        return $this->render('category/add.html.twig', ['categoryForm' => $form->createView()]);
    }

    /**
     * @Route("/category/add", name="add_category_process")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCategoryProcess(Request $request)
    {
        // Is authenticated to add category
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not authenticated to access this URL address');

            return $this->redirectToRoute('all_products');
        }
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            $repository = $this->getDoctrine()->getRepository(Category::class);
//            $categoryDB = $repository->findOneBy(['name' => $category->getName()]);
//            if ($categoryDB) {
//                $category = $categoryDB;
//            }
//
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('all_products');
        }

        return $this->render('category/add.html.twig', ['categoryForm' => $form->createView()]);
    }


    /**
     * @Route("/category/{id}", name="category_products")
     * @param Category $category
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewProductsByCategory(Category $category, Request $request)

    {
//        $paginator = $this->get('knp_paginator');
//
//        $query = $this->getDoctrine()
//            ->getRepository("OnlineShopBundle:Product")
//            ->createQueryBuilder('p')
//            ->select('p');
//
//        $pagination = $paginator->paginate(
//            $query->getQuery(),
//            $request->query->get('page', 1),
//            self::NUM_RESULTS
//        );
        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();


        return $this->render("category/one.html.twig",
            [
                "category" => $category,
                "max_promotion" => $max_promotion,
                "calc" => $calc
                // "pagination" => $pagination
            ]
        );
    }

    /**
     * @Route("/categories", name="all_categories")
     * @param Request $request
     *
     */
    public function viewCategories(Request $request)
    {

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render("category/all_categories.html.twig", ["categories" => $categories]);

    }
}
