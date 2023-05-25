<?php

namespace App\Repository;

use App\Entity\JobSector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobSector>
 *
 * @method JobSector|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobSector|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobSector[]    findAll()
 * @method JobSector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobSectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobSector::class);
    }

    public function add(JobSector $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JobSector $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNextId(): ?int
    {
        /** @var JobSector|null $last */
        $last = $this->createQueryBuilder('job_sector')
            ->orderBy('job_sector.id', 'DESC')
            ->getQuery()
            ->getResult();

        if ($last) {
            return $last[0]->getId() + 1;
        }

        return 1;
    }


    /**
     * @return JobSector[] Returns an array of JobSector objects
     */
    public function getAllDistinct(): array
    {
        return $this->createQueryBuilder('j')
            ->groupBy('j.name')
            ->orderBy('j.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?JobSector
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
