<?php

namespace App\Controller;

use App\Controller\KoAbsctractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

  class ApiLoginController extends KoAbsctractController 
  {
      #[Route('/api/login', name: 'api_login', methods: ["POST"])]
     public function login(Request $request, JWTTokenManagerInterface $jwtinterface, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
     {
        $email = $request->request->get("email");
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        if (!$email || !$username || !$password) {
            return $this->json(["error" => "bad_request"], 400);
        }
        $userRepo = $doctrine->getRepository(User::class);
        $user = null;
        if (!($user = $userRepo->findOneBy(["email" => $email])) || !($user = $userRepo->findOneBy(["username" => $username])) || !($passwordHasher->verify($user->getPassword(), $password))) {
            return $this->json(["error" => "bad credential"], 400);
        }
        $user->setTokenPass($jwtManager->create($user));

        return $this->json([
             "token" => $user->getTokenPass()
          ]);
      }

    #[Route('api/user', name: 'app_api_getUser', methods: ["GET"])]
    public function displayUser(Request $request) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        return $this->json(user);
    }
    
    #[Route("api/user", name: "app_api_updateUser", methods: ["PUT"])]
    public function updateUser(Request $request) {
        $user = $this->getUserFromToken($request);
        if ($user == null) {
            return $this->json(["error" => "Unauthorized"], 401);
        }
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $lastname = $request->request->get('lastname');
        $firstname = $request->request->get('firstname');
        if (!$usrname || !$email || !$password || !$lastname || !$firstname) {
            return $this->json(["error" => "missing_params"], 400);
        }
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getRepository(User::class);
        if (($usertmp = $userRepo->findOneBy(["email" => $email])) || ($usertmp = $userRepo->findOneBy(["username" => $username])) && $usertmp !== $user) {
            return $this->json(["error" => "email already existe"], 400);
        }
        $user = new User();
        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setTokenPass($jwtManager->create($user));
        $em->persist($user);
        $em->flush();
        return $this->json(["token"=> $user->getTokenPass()]);
    }

    #[Route('api/register', name: 'app_api_register', methods: ["POST"])]
    public function register(Request $request, JWTTokenManagerInterface $jwtManager, UserPasswordHasherInterface $passwordHasher) :Response
    {
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $lastname = $request->request->get('lastname');
        $firstname = $request->request->get('firstname');
        if (!$username || !$email || !$password || !$lastname || !$firstname) {
            return $this->json(["error" => "missing_params"], 400);
        }
        $em = $this->getDoctrine()->getManager();
        $userRepo = $this->getRepository(User::class);
        if ($userRepo->findOneBy(["email" => $email]) || $userRepo->findOneBy(["username" => $username])) {
            return $this->json(["error" => "email already existe"], 400);
        }
        $user = new User();
        $user->setUsername($username);
        $user->setLastname($lastname);
        $user->setFirstname($firstname);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setEmail($email);
        $user->setTokenPass($jwtManager->create($user));
        $em->persist($user);
        $em->flush();
        return $this->json(["token"=> $user->getTokenPass()]);
    }
  }