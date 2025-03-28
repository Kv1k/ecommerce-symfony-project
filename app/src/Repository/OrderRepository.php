<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Order $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Order $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function serializer($order) {
        if ($order instanceof Order) {
            $resp = ["id" => $order->getId(), "total" => $order->getTotalPrice()];
           foreach ($order->getProducts() as $val) {
                $resp["products"][] = [
                    "id" => $val->getId(),
                    "price" => $val->getPrice(),
                    "name" => $val->getName(),
                    "description" => $val->getDescription(),
                    "quantity" => $order->getQuantity()[$val->getId()],
                ];
           }
           return $resp;
        }
        $resp = [];
        $c = -1;
        while(++$c < count($order)) {
            $tmp = ["id" => $order[$c]->getId(), "total" => $order[$c]->getTotalPrice()];
            foreach ($order[$c]->getProducts() as $val) {
                $tmp["products"][] = [
                    "id" => $val->getId(),
                    "price" => $val->getPrice(),
                    "name" => $val->getName(),
                    "description" => $val->getDescription(),
                    "quantity" => $order[$c]->getQuantity()[$val->getId()],
                ];
            }
            $resp[] = $tmp;
        }
        return $resp;
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
