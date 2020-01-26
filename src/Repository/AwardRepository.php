<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Award;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class AwardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Award::class);
    }

    public function getAvailablePrizes(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.amount > 0')
            ->andWhere('p.type = :type')
            ->setParameter('type', Award::TYPE_PRIZE)
            ->getQuery()
            ->execute();
    }
}
