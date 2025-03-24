<?php

namespace App\Controller;
use App\Entity\Order;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\KoAbsctractController;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends KoAbsctractController
{
    #[Route('/api/orders', name: 'app_order', methods: ["GET"])]
    public function getOrders(Request $request): Response
    {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $orderRepo = $this->getRepository(Order::class);
        $orders = $orderRepo->findBy(["user" => $user]);
        return $this->json($orderRepo->serializer($orders));
    }


    #[Route('/api/orders/{id}', name: 'app_api_getOrder', methods: ['GET'])]
    public function getOrder(Request $request, $id) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $orderRepo = $this->getRepository(Order::class);
        $order = $orderRepo->findOneBy(["user" => $user, "id" => $id]);
        return $this->json($orderRepo->serializer($order));
    }
}
