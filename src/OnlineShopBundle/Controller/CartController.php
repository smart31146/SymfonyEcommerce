<?php

namespace OnlineShopBundle\Controller;

use OnlineShopBundle\Entity\Cart;
use OnlineShopBundle\Entity\CartProduct;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/cart/add", name="cart_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {

        $manager = $this->getDoctrine()->getManager();
        $session = $this->get('session');


        $id_cart = $session->get('id_cart', false);

        if (!$id_cart) {
            $cart = new Cart();
            $cart->setUserId($this->getUser());
            $cart->setDateCreated(new \DateTime());
            $cart->setDateUpdated(new \DateTime());

            $manager->persist($cart);
            $manager->flush();

            $session->set('id_cart', $cart->getId());
        }

        $cart = $this->getDoctrine()->getRepository('OnlineShopBundle:Cart')->find($session->get('id_cart', false));

        $products = $request->get('products');

        foreach ($products as $id_product) {
            $product = $this->getDoctrine()->getRepository('OnlineShopBundle:Product')->find($id_product);

            if ($product) {

                $cp = $this->getDoctrine()->getRepository('OnlineShopBundle:CartProduct')->findOneBy([
                    'cart' => $cart,
                    'product' => $product
                ]);

                if (!$cp) {
                    $cp = new CartProduct();
                    $cp->setCart($cart);
                    $cp->setProduct($product);
                    $cp->setQty(1);
                } else {
                    $cp->setQty($cp->getQty() + 1);
                }


                $manager->persist($cp);
            }
        }

        $cart->setDateUpdated(new \DateTime());

        $manager->persist($cart);

        $manager->flush();

        $this->addFlash("addCart", "The product was successfully added to your cart");

        return $this->redirectToRoute('cart_list');
    }


    /**
     * @Route("/cart/list", name="cart_list")
     */
    public function listAction()
    {
        $cart = $this->getDoctrine()
            ->getRepository(Cart::class)
            ->findBy([]);

        $cart_product = $this->getDoctrine()
            ->getRepository(CartProduct::class)
            ->findBy([]);

        $user = $this->getUser();

        $calc = $this->get('price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        return $this->render("cart/list.html.twig",
            [
                "cart" => $cart,
                "cart_product" => $cart_product,
                "user" => $user,
                "max_promotion" => $max_promotion,
                "calc" => $calc
            ]
        );
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_cart_product_process")
     * @param CartProduct $cartProduct
     * @return Response
     */
    public function removeProduct(CartProduct $cartProduct)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($cartProduct);
        $em->flush();

        $this->addFlash("remove", "The product was successfully removed form your cart");

        return $this->redirectToRoute("cart_list");
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @return Response
     */
    public function checkOutCart()
    {

        $this->addFlash("checkOut", "Your order is being processed");

        return $this->render("cart/checkout.html.twig");


    }
}
