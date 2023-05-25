<?php
/**
 * @author hR.
 */

namespace App\DataInjector;

use App\Tools\PhpSpreadsheet;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractBaseInjector
{
    /**
     * @var PhpSpreadsheet
     */
    protected $phpSpreadsheet;
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var SluggerInterface
     */
    protected $slugger;

    public function __construct(
        EntityManagerInterface $entityManager,
        PhpSpreadsheet $phpSpreadsheet,
        ParameterBagInterface $parameterBag,
        SluggerInterface $slugger
    )
    {
        $this->phpSpreadsheet = $phpSpreadsheet;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * @param string $entityName
     * @param string $realTableName
     *
     * @throws Exception
     */
    public function removeAllEntity(string $entityName, string $realTableName)
    {
        $entities = $this->entityManager->getRepository($entityName)->findAll();
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();

        //reset auto increment
        $this->entityManager->getConnection()
            ->prepare("ALTER TABLE $realTableName AUTO_INCREMENT = 1")
            ->executeQuery();
    }

    /**
     * @param int $key
     *
     * @return int
     */
    public static function addOneToKeyTab(int $key): int
    {
        return $key + 1;
    }
}
