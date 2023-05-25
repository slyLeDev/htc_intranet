<?php

namespace App\Repository;

use App\Entity\InterviewCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterviewCategory>
 *
 * @method InterviewCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterviewCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterviewCategory[]    findAll()
 * @method InterviewCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterviewCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterviewCategory::class);
    }

    public function add(InterviewCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InterviewCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNextId(): ?int
    {
        /** @var InterviewCategory|null $last */
        $last = $this->createQueryBuilder('itw_category')
            ->orderBy('itw_category.id', 'DESC')
            ->getQuery()
            ->getResult();

        if ($last) {
            return $last[0]->getId() + 1;
        }

        return 1;
    }

//    /**
//     * @return InterviewCategory[] Returns an array of InterviewCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InterviewCategory
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
