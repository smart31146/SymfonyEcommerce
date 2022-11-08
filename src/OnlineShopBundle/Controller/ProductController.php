<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Product;
use OnlineShopBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Class ProductController
 * @package OnlineShopBundle\Controller
 */
//@Security("has_role('ROLE_USER')")
class ProductController extends Controller
{
    const NUM_RESULTS = 5;

    /**
     * @Route("/products", name="all_products")
     * @var $products Product[]
     *
     * @return Response
     */
    public function viewAll(Request $request)
    {
        $paginator = $this->get('knp_paginator');

        $query = $this->getDoctrine()
            ->getRepository("OnlineShopBundle:Product")
            ->createQueryBuilder('p')
            ->select('p')
            ->where('p.quantity > 0');

        $query_admin = $this->getDoctrine()
            ->getRepository("OnlineShopBundle:Product")
            ->createQueryBuilder('p')
            ->select('p');

        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1),
            self::NUM_RESULTS
        );

        $pagination_admin = $paginator->paginate(
            $query_admin->getQuery(),
            $request->query->get('page', 1),
            self::NUM_RESULTS
        );

//        $products = $this->getDoctrine()
//            ->getManager()
//            ->getRepository(Product::class)
//            ->getAvailable();

        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        return $this->render('products/view_all.html.twig',
            [
//                "products" => $products,
                'user' => $this->getUser(),
                "pagination" => $pagination,
                "pagination_admin" => $pagination_admin,
                "max_promotion" => $max_promotion,
                "calc" => $calc
            ]);
    }

    /**
     *
     * @Route("/products/show/{id}", name="product_show")
     * @Method("GET")
     * @param Product $product
     * @return Response
     */
    public function showAction(Product $product)
    {
        $user = $this->getUser();
        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();
        return $this->render('products/show.html.twig', array(
            'product' => $product,
            'user' => $user,
            "max_promotion" => $max_promotion,
            "calc" => $calc

        ));
    }

    /**
     * @Route("/products/add", name="add_product_form")
     * @Method("GET")
     *
     * @return Response
     */
    public function add()
    {
        // Is authenticated to add
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(ProductType::class);
        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/products/add", name="add_product_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
    {
        // Is authenticated to add
        if (!$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('all_products');
        }

        $product = new Product();
        $form = $this->createForm(
            ProductType::class,
            $product
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());

            /** @var UploadedFile $file */
            $file = $product->getImageForm();

            if (!$file) {
                $form->get('image_form')->addError(new FormError('Image is required'));
            } else {
                $filename = md5($product->getName() . '' . $product->getCreatedOn()->format("Y-m-d H:i:s"));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/pics/products/',
                    $filename
                );

                $product->setImage($filename);

                $entityManager = $this->getDoctrine()
                    ->getManager();
                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash("info", "Product with name \"" . $product->getName() . "\" was added successfully");

                return $this->redirectToRoute("all_products");
            }
        }

        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product_form")
     * @Method("GET")
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product)
    {
        // Is authenticated to edit
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('all_products');
        }

        $form = $this->createForm(
            ProductType::class,
            $product
        );
        return $this->render("products/edit.html.twig",
            ["productForm" => $form->createView()]);
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product_process")
     * @Method("POST")
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function editProcess(Product $product, Request $request)
    {
        // Is authenticated to edit
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('all_products');
        }
        $form = $this->createForm(
            ProductType::class,
            $product
        );
        $form->handleRequest($request);
        if ($form->isValid()) {

            if ($product->getImageForm() instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $product->getImageForm();

                $filename = md5($product->getName() . '' . $product->getCreatedOn()->format('Y-m-d H:i:s'));

                $file->move(
                    $this->get('kernel')->getRootDir() . '/../web/pics/products/',
                    $filename
                );

                $product->setImage($filename);
            }

            $em = $this->getDoctrine()
                ->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("info", "Product with name \"" . $product->getName() . "\" was edited successfully");

            return $this->redirectToRoute("all_products");
        }
        return $this->render("products/edit.html.twig",
            ["productForm" => $form->createView()]);
    }


    /**
     * @Route("/products/delete/{id}", name="delete_product_process")
     * @Method("POST")
     * @param Product $product
     * @return Response
     */
    public function deleteProduct(Product $product)
    {
        // Is authenticated to delete
        if ($product->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted('ROLE_ADMIN', $this->getUser()) &&
            !$this->isGranted('ROLE_MODERATOR', $this->getUser())
        ) {
            $this->get('session')->getFlashBag()->add('error', 'You are not the owner of this product');

            return $this->redirectToRoute('all_products');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash("delete", "The product was successfully deleted");

        return $this->redirectToRoute("all_products");
    }
}