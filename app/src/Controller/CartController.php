<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Card;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\KoAbsctractController;

class CartController extends KoAbsctractController
{
    #[Route('/api/carts', name: 'app_cart')]
    public function getShoppingCart(Request $request): Response
    {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $em = $this->getDoctrine()->getManager();
        $cardRepo = $this->getRepository(Card::class);
        $card = $cardRepo->findOneBy(["user" => $user]);
        if ($card == null) {
            return $this->json([], 204);
        }
        return $this->json($cardRepo->serializer($card));
    }

    #[Route('/api/carts/{productId<\d+>}', name: "app_api_cart_addProduct", methods: ["PUT"])]
    public function addProduct(Request $request, $productId) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $producRepo = $this->getRepository(Product::class);
        $product = $producRepo->findOneBy(["id" => $productId]);
        if ($product == null || $product->getUser() == null) {
            return $this->json(["error" => "product not found"], 400);
        }
        $em = $this->getDoctrine()->getManager();
        $cardRepo = $this->getRepository(Card::class);
        $card = $cardRepo->findOneBy(["user" => $user]);
        if ($card == null) {
            $card = new Card();
            $card->setUser($user);
            $em->persist($card);
            $em->flush();
        }
        $card->addProduct($product);
        $em->persist($card);
        $em->flush();
        return $this->json($cardRepo->serializer($card));
    }

    #[Route('/api/carts/{productId}', name: "app_api_cart_rmProduct", methods: ["DELETE"])]
    public function rmProduct(Request $request, $productId) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $productRepo = $this->getRepository(Product::class);
        $product = $productRepo->findOneBy(["id" => $productId]);
        if ($product == null || $product->getUser() == null) {
            return $this->json(["error" => "product not found"], 400);
        }
        $em = $this->getDoctrine()->getManager();
        $cardRepo = $this->getRepository(Card::class);
        $card = $cardRepo->findOneBy(["user" => $user]);
        if ($card == null) {
            return $this->json([], 204);
        }
        $card->removeProduct($product);
        $em->persist($card);
        $em->flush();
        return $this->json($cardRepo->serializer($card));
    }

    #[Route('/api/carts/validated', name: 'app_api_cart_validated', methods: ["PUT"])]
    public function validateCart(Request $request) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $em = $this->getDoctrine()->getManager();
        $cardRepo = $this->getRepository(Card::class);
        $card = $cardRepo->findOneBy(["user" => $user]);
        if ($card == null || count($card->getProducts()) == 0) {
            return $this->json(["error" => "no card or no product inside"], 409);
        }
        $order = new Order();
        $order->setUser($user);
        $orderRepo = $this->getRepository(Order::class);
        $tot = 0;
        $c = -1;

        while (++$c < count($card->getProducts()) ) {
            $product = $card->getProducts()[$c];
            $tot += $product->getPrice() * $card->getQuantity()[$product->getId()];
            $order->addProduct($product);
            $order_q = $order->getQuantity();
            $order->setCreatedAt(new \DateTimeImmutable());
            $order_q[$product->getId()] = $card->getQuantity()[$product->getId()];
            $order->setQuantity($order_q);
        }
        $order->setTotalPrice($tot);
        $em->persist($order);
        $em->remove($card);
        $em->flush();
        return $this->json($orderRepo->serializer($order));
    }
}
