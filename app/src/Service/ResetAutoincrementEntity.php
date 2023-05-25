<?php
/**
 * @author hR.
 */

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class ResetAutoincrementEntity
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    public function resetAutoincrement(string $tableName)
    {
        //reset auto increment
        $this->entityManager->getConnection()
            ->prepare("ALTER TABLE $tableName AUTO_INCREMENT = 1")
            ->executeQuery();
    }
}