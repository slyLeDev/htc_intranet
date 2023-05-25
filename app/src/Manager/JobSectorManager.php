<?php
/**
 * @author hR.
 */
namespace App\Manager;

use App\Entity\JobSector;
use App\Tools\PhpSpreadsheet;
use App\Tools\StringTools;
use Doctrine\ORM\EntityManagerInterface;

/** JobSectorManager */
class JobSectorManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $sector
     *
     * @return JobSector|mixed|object|null
     */
    public function getJobSector(string $sector)
    {
        $checkSector = $this->entityManager
            ->getRepository(JobSector::class)
            ->findOneBy(['name' => $sector]);
        $jobSector = $checkSector ?? new JobSector();
        $jobSector->setName($sector);
        if (!$jobSector->getId()) {
            $this->entityManager->persist($jobSector);
        }

        $this->entityManager->flush();

        return $jobSector;
    }

    /**
     * @param string $sectors
     *
     * @return array
     */
    public function retrieveJobSector(string $sectors): array
    {
        $theData = [];
        $dataTmp = trim($sectors);
        if (PhpSpreadsheet::cellValueIsNotEmpty($dataTmp) && !StringTools::containsNumber($dataTmp)) {
            if (false === strpos($dataTmp, '(') && false === strpos($dataTmp, ')') &&
                false === strpos($dataTmp, '[') && false === strpos($dataTmp, ']')) {
                $sectors = preg_split('/(;|,|-| ; | ;|; | , |, | ,| - |- | -|\/| \/|\/)/', $dataTmp);
                foreach ($sectors as $sector) {
                    $sector = StringTools::cleanSpecialCharacter($sector);
                    $sector = ucfirst(strtolower($sector));
                    if (PhpSpreadsheet::cellValueIsNotEmpty($sector)) {
                        $jobSector = $this->getJobSector($sector);
                        $theData[] = $jobSector;
                    }
                }
            }

            if ((false !== strpos($dataTmp, '(')) && (false !== strpos($dataTmp, ')')) ||
                (false !== strpos($dataTmp, '[')) && (false !== strpos($dataTmp, ']'))) {
                $dataTmp = StringTools::cleanSpecialCharacter($dataTmp);
                $dataTmp = ucfirst(strtolower($dataTmp));
                $jobSector = $this->getJobSector($dataTmp);
                $theData[] = $jobSector;
            }
        }

        return $theData;
    }
}
