<?php

namespace App\Controller;
use Doctrine\ORM\Query;
use App\Controller\KoAbsctractController;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;

class ProductController extends KoAbsctractController 
{
    #[Route('/api/products', name: 'app_product', methods: ["GET"])]
    public function getAll(): Response
    {
        $productRepo = $this->getRepository(Product::class);
        $products = $productRepo->findAll();
        return $this->json($productRepo->serializer($products));
    }

    #[Route('/api/products/{id}', name: 'app_api_getProduct', methods: ["GET"])]
    public function getProduct($id, ProductRepository $productRepo) {
        $product = $productRepo->find($id); 
        if ($product == null) {
            return $this->json(["error"=> "bad_request", "message"=> "ressource not found"], 400);
        }
        return $this->json($productRepo->serializer($product));
    }

    #[Route('/api/products', name: 'app_api_postProducts', methods: ["POST"])]
    public function postProduct(Request $request) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $name = $request->request->get("name");
        $desc = $request->request->get("description");
        $picture = $request->files->get("picture");
        $price = $request->request->get("price");
        if (!$name || !$desc || !$price) {
            return $this->json(["error" => "bad request"], 400);
        }
        if ($picture !== null) {
            $picture = $picture;
            $picture = 'data:image/' . $picture->getMimeType() . ';base64,' . base64_encode($picture->getContent());
        } else {
            $picture = null;
        }
        $product = new Product();
        $product->setName($name);
        $product->setDescription($desc);
        $product->setPhoto($picture);
        $product->setPrice($price);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return $this->json(["id" => $product->getId(), "price" => $product->getPrice(), "description" => $product->getDescription(), "picture" => $product->getPhoto()]);
    }

    #[Route('/api/products/{id}', name: 'app_api_putProduct', methods: ["PUT"])]
    public function putProduct(Request $request, $id) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $producRepo = $this->getRepository(Product::class);
        $product = $producRepo->findOneBy(["id" => $id]);
        if ($product == null || $product->getUser() == null) {
            return $this->json(["error" => "product not found"], 400);
        } else if ($product->getUser()[0] !== $user ) {
            return $this->json(["error" => "bad credential"], 403);
        }
        $content = json_decode($request->getContent(), True);
        if (!isset($content["name"]) || !isset($content["description"]) || !isset($content["price"])) {
            return $this->json(["error" => "bad request"], 400);
        }
        $name = $content["name"];
        $desc = $content["description"];
        $price = $content["price"];
        if (!$name || !$desc || !$price) {
            return $this->json(["error" => "bad request"], 400);
        }
        $product->setName($name);
        $product->setDescription($desc);
        if (isset($content["picture"])) {
            $product->setPhoto($content["picture"]);
        }
        $product->setPrice($price);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return $this->json(["id" => $product->getId(), "name" => $product->getName(), "price" => $product->getPrice(), "description" => $product->getDescription(), "picture" => $product->getPhoto()]);
    }

    #[Route('/api/products/{id}', name: 'app_api_deleteProduct', methods:["DELETE"])]
    public function deleteProduct(Request $request, $id) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $producRepo = $this->getRepository(Product::class);
        $product = $producRepo->findOneBy(["id" => $id]);
        if ($product == null || $product->getUser() == null) {
            return $this->json(["error" => "product not found"], 400);
        } else if ($product->getUser()[0] !== $user ) {
            return $this->json(["error" => "bad credential"], 403);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        return $this->json([], 204);
    }
}
