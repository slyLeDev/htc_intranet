<?php
/**
 * @author hR.
 */

namespace App\DataInjector;

use App\Entity\JobSector;
use App\Entity\Profile;
use App\Interfaces\InjectDataInterface;
use App\Manager\JobSectorManager;
use App\Tools\MailTools;
use App\Tools\PhpSpreadsheet;
use App\Tools\StringTools;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * ReceivedProfileInjector
 */
class ReceivedProfileInjector extends AbstractBaseInjector implements InjectDataInterface
{
    /**
     * @var JobSectorManager
     */
    private $jobSectorManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        PhpSpreadsheet $phpSpreadsheet,
        ParameterBagInterface $parameterBag,
        SluggerInterface $slugger,
        JobSectorManager $jobSectorManager
    ) {
        parent::__construct($entityManager, $phpSpreadsheet, $parameterBag, $slugger);
        $this->jobSectorManager = $jobSectorManager;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    function cleanup()
    {
        //remove all job sector
        $this->removeAllEntity('App\Entity\JobSector', 'job_sector');
        //remove all received profile
        $this->removeAllEntity('App\Entity\Profile', 'profile');
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception|\PhpOffice\PhpSpreadsheet\Exception
     */
    public function inject(SymfonyStyle $symfonyStyle, bool $simulate)
    {
        $symfonyStyle->note('Cleanup JobSector and Profile ...');
        $this->cleanup();
        $symfonyStyle->note('Cleanup DONE, retrieving received profile data ...');
        $data = $this->phpSpreadsheet::getData($this->getFilePath(), PhpSpreadsheet::PROCEDURAL_MODE);
        $totalCountData = count($data);
        $progressBar = $symfonyStyle->createProgressBar($totalCountData);
        $progressBar->start();
        $skipFirstRow = 0;
        $countInjected = 0;
        $countInvalidEmail = 0;
        $emptyLine = 0;
        $line = 1;
        $lineErrorFileName = $this->parameterBag->get('data_injector_base_files_path').'profile_received_error_'.
            (new DateTime())->format('dmY_His').'.csv';
        $fileError = fopen($lineErrorFileName, 'wb');
        foreach ($data as $lineNumber => $profileReceived) {
            if (0 === $skipFirstRow) {
                ++$skipFirstRow;
                $progressBar->advance();
                continue;
            }

            ++$line;
            $profileReceivedTmp = $profileReceived;
            if (!PhpSpreadsheet::cellValueIsNotEmpty($profileReceived[self::addOneToKeyTab(1)]) &&
                !PhpSpreadsheet::cellValueIsNotEmpty($profileReceived[self::addOneToKeyTab(2)])
            ) {
                ++$emptyLine; //empty line, skip
                continue;
            }

            /*if ($profileReceived[3] != 'sarindraniainaluc@libero.it') {
                continue;
            }*/

            /*if ($profileReceived[3] != 'andriamiarimananayolande@gmail.com') {
                continue;
            }*/

            /*if ($profileReceived[2] != 'RALAIVAHINY Tokiniaina') {
                continue;
            }*/

            $profileReceived = $this->buildData($profileReceived, ($lineNumber+1));
            $fullName = $profileReceived[self::addOneToKeyTab(1)];
            $fullNameSlugged = $this->slugger->slug($fullName);
            $email = $profileReceived[self::addOneToKeyTab(2)];
            $haveFullName = PhpSpreadsheet::cellValueIsNotEmpty($fullName);
            $haveEmail = PhpSpreadsheet::cellValueIsNotEmpty($email);
            //dd($profileReceived, $fullName, $email, $profileReceived[self::addOneToKeyTab(9)]);
            if ($haveFullName || $haveEmail) {
                try {
                    $queryData = ['fullNameSlugged' => $fullNameSlugged];
                    if ($haveEmail) {
                        $queryData = ['email' => $email];
                    }
                    $checkProfile = $this->getRepository()->findOneBy($queryData); //email is unique
                    $profile = $checkProfile ?? new Profile();
                    $profile
                        ->setReceivedAt($profileReceived[0])
                        ->setFullName($profileReceived[self::addOneToKeyTab(1)])
                        ->setFullNameSlugged($fullNameSlugged)
                        ->setPhone($profileReceived[self::addOneToKeyTab(3)])
                        ->setYearsOld($profileReceived[self::addOneToKeyTab(4)])
                        ->setGender($profileReceived[self::addOneToKeyTab(5)])
                        ->setActuallyJobTitle($profileReceived[self::addOneToKeyTab(6)])
                        ->setHopeJobTitle($profileReceived[self::addOneToKeyTab(7)])
                        ->setYearOfExperience($profileReceived[self::addOneToKeyTab(8)]['text'])
                        ->setXpYear($profileReceived[self::addOneToKeyTab(8)]['number'])
                        ->setDegree($profileReceived[self::addOneToKeyTab(10)])
                        ->setLocality($profileReceived[self::addOneToKeyTab(11)])
                        ->setComment($profileReceived[self::addOneToKeyTab(12)])
                        ->setCurriculumVitae($profileReceived[self::addOneToKeyTab(13)])
                    ;

                    if (PhpSpreadsheet::cellValueIsNotEmpty($email) &&
                        filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $profile->setEmail($email);
                    }

                    /** @var JobSector $jobSector */
                    foreach ($profileReceived[self::addOneToKeyTab(9)] as $jobSector) {
                        $profile->addSector($jobSector);
                    }

                    $profile->setStatus(Profile::RECEIVED);
                    $profile->buildIsNeededInformationIsComplete();

                    if (!$profile->getId()) {
                        ++$countInjected;
                        if (!$simulate) {
                            $this->entityManager->persist($profile);
                        }
                    }
                    if (!$simulate) {
                        $this->entityManager->flush();
                    }
                } catch (Exception $e) {
                    throw new Exception('Error at line '.$line.' : '.$e->getMessage());
                }
            }

            if (!PhpSpreadsheet::cellValueIsNotEmpty($fullName) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                ++$countInvalidEmail; //email invalid
                fputcsv($fileError, $profileReceivedTmp);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        fclose($fileError);
        $symfonyStyle->success(
            'Received profile imported. Done : '.$countInjected.' sur '.
            $totalCountData.' / Email empty : '.$countInvalidEmail.' / Empty line : '.$emptyLine
        );
    }

    /**
     * Build data by cell.
     *
     * @param array  $lineData
     * @param int    $lineNumber
     * @param string $sheetName
     * @param bool   $simulate
     *
     * @return array
     */
    public function buildData(array $lineData, int $lineNumber, string $sheetName = '', bool $simulate = false): array
    {
        $buildedData = [];
        foreach ($lineData as $key => $data) {
            $theData = null;
            $data = PhpSpreadsheet::cellValueIsNotEmpty(trim($data)) ? html_entity_decode(trim($data)) : '';
            switch ($key) {
                case 0: //received date
                    $theData = DateTimeImmutable::createFromFormat('d/m/Y h:i:s', $data) ?:
                        DateTimeImmutable::createFromFormat('d/m/Y', '01/01/2020');
                    break;
                case self::addOneToKeyTab(1): //fullName
                    $theData = ucwords(strtolower(str_replace([':', ' :', ' :'], ['', '', ''], $data)));
                    break;
                case self::addOneToKeyTab(2): //email
                    $phoneNumberError = MailTools::extractFromString($lineData[self::addOneToKeyTab(3)]);
                    $theData = !empty($phoneNumberError) ? $phoneNumberError : MailTools::extractFromString($data);

                    break;
                case self::addOneToKeyTab(3): //phone number
                    $theData = $data;
                    if (!empty(MailTools::extractFromString($lineData[self::addOneToKeyTab(3)]))) {
                        $theData = trim($lineData[2]);
                    }
                    break;
                case self::addOneToKeyTab(4): //years old
                    $theData = is_numeric($data) ?
                        (int) filter_var($data, FILTER_SANITIZE_NUMBER_INT) :
                        null;
                    break;
                case self::addOneToKeyTab(5): //gender
                    $theData = Profile::recognizeGender($data);
                    break;
                case self::addOneToKeyTab(6): //actually job
                    $tmpData = $data;
                    $theData = null;
                    if (strlen($tmpData) > 1) {
                        $theData = ucfirst($data);
                    }
                    break;
                case self::addOneToKeyTab(7): //hope job title
                    $theData = ucfirst($data);
                    break;
                case self::addOneToKeyTab(8): //year of experience
                    $dataToBuild = strtolower($data);
                    $dataToBuild = str_replace(',', '.', $dataToBuild);
                    $theData['text'] = StringTools::extractNumberOfExpFromString($dataToBuild);
                    $theData['number'] = StringTools::extractNumberOfExpFromString($dataToBuild, true);
                    break;
                case self::addOneToKeyTab(9): //sectors
                    $theData = $this->jobSectorManager->retrieveJobSector($data);
                    break;
                case self::addOneToKeyTab(10): //degree
                    $theData = ucfirst($data);
                    break;
                case self::addOneToKeyTab(11): //locality
                    $theData = ucfirst($data);
                    break;
                case self::addOneToKeyTab(12): //comment
                    $theData = ucfirst($data);
                    break;
                case self::addOneToKeyTab(13): //cv link
                    $theData = $data;
                    break;
            }

            $buildedData[$key] = $theData;
        }

        return $buildedData;
    }

    public function getFilePath(): string
    {
        return $this->parameterBag->get('file_profile_received_path');
    }

    public function getRepository()
    {
        return $this->entityManager->getRepository(Profile::class);
    }

    function cleanData(array $arrayData)
    {
        // TODO: Implement cleanData() method.
    }
}
