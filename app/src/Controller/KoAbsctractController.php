<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class KoAbsctractController extends AbstractController {

    private $_jwtinterface = null;
    private $_doctrine = null; 

    public function __construct(JWTTokenManagerInterface $jwtinterface, ManagerRegistry $doctrine) {
        $this->_jtwinterface = $jwtinterface;
        $this->_doctrine = $doctrine;
    }


    protected function getUserFromToken($request) {
        $urep = $this->getRepository(User::class);
        $auth = $request->headers->get("authorization");
        if ($auth == null) {   
            return null;
        }
        return $urep->findOneBy(['token_pass' => $auth]);
    }

    protected function getRepository($classname) {
        return $this->_doctrine->getRepository($classname);
    }
    
    protected function getDoctrine() {
        return $this->_doctrine;
    }
}