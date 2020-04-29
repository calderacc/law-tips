<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Sign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sign::class);
    }

    public function findForImageImport(bool $emptyOnly = true, int $limit = 25, int $offset = 0): array
    {
        $qb = $this->createQueryBuilder('s');

        if ($emptyOnly) {
            $qb->where($qb->expr()->isNull('s.imageName'));
        }

        $qb
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $qb->getQuery()->getResult();
    }
}
