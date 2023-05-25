<?php
/**
 * @author hR.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct($registry, User::class);
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function add(User $entity, bool $flush = false): void
    {
        $entity->setRoles([User::ROLE_CONSULTANT]);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @param string $role
     * @param bool   $getResult
     * @param bool   $findDefaultConsultant
     * @param bool   $orderByLastActivity
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function findByRole(
        string $role,
        bool $getResult = true,
        bool $findDefaultConsultant = false,
        bool $orderByLastActivity = false
    ) {
        $role = mb_strtoupper($role);

        if ($orderByLastActivity) {
            return $this->createQueryBuilder('u')
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', "%\"$role\"%")
                ->orderBy('u.lastActivityAt', 'DESC')
                ->getQuery()
                ->getResult();
        }

        if ($getResult && $findDefaultConsultant) {
            $find = $this->createQueryBuilder('u')
                ->andWhere('u.roles LIKE :role')
                ->andWhere('u.fullName = :fullName')
                ->setParameter('role', "%\"$role\"%")
                ->setParameter('fullName', "Generated Consultant")
                ->getQuery()
                ->getOneOrNullResult();

            if (!$find) {
                $defaultConsultantUser = new User();
                $defaultConsultantUser
                    ->setFullName('Generated Consultant')
                    ->setEmail('default-consultant@human-talent-consulting.com')
                    ->setIsEnable(true)
                ;

                // encode the plain password
                $defaultConsultantUser->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $defaultConsultantUser,
                        'BFLPp444v)6h*g78'
                    )
                );

                $this->add($defaultConsultantUser, true);

                return $defaultConsultantUser;
            }

            return $find;
        }

        if ($getResult) {
            return $this->createQueryBuilder('u')
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', "%\"$role\"%")
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', "%\"$role\"%");
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function changeState(User $user)
    {
        $user->setIsEnable(!$user->isEnable());
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
