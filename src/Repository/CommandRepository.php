<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    public function findUncheckInvoice()
    {
        return $this->createQueryBuilder('command')
            ->andWhere("command.state = 'payée'")
            ->andWhere("command.pay_check = 0")
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkExpired($today)
    {
        return $this->createQueryBuilder('command')
            ->andWhere("command.limit_date < :today")
            ->setParameter('today', $today)
            ->getQuery()
            ->getResult()
        ;
    }
}
