<?php

namespace App\Repository;

use App\Entity\Agent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    // /**
    //  * @return Agent[] Returns an array of Agent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Agent
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAgentByStandardCriteria($search){
       return $this->createQueryBuilder('a')
            ->andWhere('a.firstName LIKE :search OR a.lastName LIKE :search OR a.email LIKE :search OR a.post LIKE :search')
            ->innerJoin('a.fonction','f')
            ->orWhere('f.name LIKE :search')
            ->innerJoin('f.department', 'd')
            ->orWhere('d.name LIKE :search')
            ->innerJoin('d.direction', 'dd')
            ->orWhere('dd.name LIKE :search')
            ->innerJoin('dd.unit', 'u')
            ->orWhere('u.name LIKE :search')
            ->innerJoin('u.entity', 'e')
            ->orWhere('e.name LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult()
       ;
    }

    public function findAgentBySpecificCriteria($search){
        return $this->createQueryBuilder('a')
             ->andWhere('a.firstName = :search OR a.lastName = :search OR a.email = :search OR a.post = :search')
             ->innerJoin('a.fonction','f')
             ->orWhere('f.name = :search')
             ->innerJoin('f.department', 'd')
             ->orWhere('d.name = :search')
             ->innerJoin('d.direction', 'dd')
             ->orWhere('dd.name = :search')
             ->innerJoin('dd.unit', 'u')
             ->orWhere('u.name = :search')
             ->innerJoin('u.entity', 'e')
             ->orWhere('e.name = :search')
             ->setParameter('search', $search)
             ->getQuery()
             ->getResult()
        ;
     }
}
