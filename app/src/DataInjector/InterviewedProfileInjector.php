<?php
/**
 * @author hR.
 */

namespace App\DataInjector;

use App\Entity\Interview;
use App\Entity\InterviewCategory;
use App\Entity\JobSector;
use App\Entity\Profile;
use App\Entity\User;
use App\Interfaces\InjectDataInterface;
use App\Manager\JobSectorManager;
use App\Tools\MailTools;
use App\Tools\PhpSpreadsheet;
use App\Tools\ResumeTools;
use App\Tools\StringTools;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class InterviewedProfileInjector extends AbstractBaseInjector implements InjectDataInterface
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
     * @param SymfonyStyle $symfonyStyle
     * @param bool         $simulate
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     */
    function inject(SymfonyStyle $symfonyStyle, bool $simulate)
    {
        //$symfonyStyle->note('Cleanup JobSector and Profile ...');
        //$this->cleanup();
        $symfonyStyle->note('Retrieving interviewed profile data ...');
        $toCleanData = $this->phpSpreadsheet::getData($this->getFilePath(), PhpSpreadsheet::SPREADSHEET_MODE, false, true);
        unset($toCleanData['INDEX']);
        $data = $this->cleanData($toCleanData);
        $progressBar = $symfonyStyle->createProgressBar(count($data));
        $blacklisted = $data['LISTE NOIRE'];
        $injected = 0;
        $maj = 0;
        $blacklistedCount = 0;
        $interviewedCount = 0;
        $logsHeader = ['Nom de la feuille', 'Numero de ligne', 'Obsérvation sur le data des expériences'];
        $logs = [];
        $defaultConsultant = $this->entityManager->getRepository(User::class)->findByRole(
            'ROLE_CONSULTANT',
            true,
            true
        );
        $symfonyStyle->note('Begin ...');
        //dd(array_keys($data['marketing webcommunication']));
        foreach ($data as $sheetName => $itwProfile) {
            if ('LISTE NOIRE' === $sheetName) {
                $progressBar->advance();
                continue;
            }
            $progressBar->advance();
            foreach ($itwProfile as $lineNumber => $interviewedProfile) {
                $interviewedProfile = $this->buildData($interviewedProfile, ($lineNumber+2), $sheetName, $simulate);

                $fullName = $interviewedProfile[1];
                $email = $interviewedProfile[2];
                if (PhpSpreadsheet::cellValueIsNotEmpty($fullName) || PhpSpreadsheet::cellValueIsNotEmpty($email)) {
                    $fullNameSlugged = $this->slugger->slug($fullName);
                    $checkProfile = $this->getRepository()->findOneBy(['fullNameSlugged' => $fullNameSlugged]);
                    if (!$checkProfile) {
                        $checkProfileByEmail = $this->getRepository()->findOneBy(['email' => $email]);
                    }
                    $profile = $checkProfile ?? $checkProfileByEmail ?? new Profile();
                    $profile
                        ->setLastMajAt($interviewedProfile[0])
                        ->setFullName($interviewedProfile[1])
                        ->setFullNameSlugged($fullNameSlugged)
                        ->setFieldWithControl('email', $interviewedProfile[2])
                        ->setFieldWithControl('phone', $interviewedProfile[3])
                        ->setFieldWithControl('yearsOld', $interviewedProfile[4])
                        ->setFieldWithControl('gender', $interviewedProfile[5])
                        ->setFieldWithControl('actuallyJobTitle', $interviewedProfile[7])
                        ->setFieldWithControl('hopeJobTitle', $interviewedProfile[8])
                        ->setYearOfExperience($interviewedProfile[9]['text'])
                        ->setXpYear($interviewedProfile[9]['number'])
                        ->setFieldWithControl('degree', $interviewedProfile[11])
                        ->setFieldWithControl('locality', $interviewedProfile[12])
                        ->setSalaryExpectationMin($interviewedProfile[13]['min'])
                        ->setSalaryExpectationMax($interviewedProfile[13]['max'])
                        ->setSalaryExpectationFix($interviewedProfile[13]['fix'])
                        ->setComment($interviewedProfile[17])
                    ;

                    //sector/domain and experience
                    if (count($interviewedProfile[10]) > 0) {
                        $profile->getSectors()->clear();
                    }
                    /** @var JobSector $jobSector */
                    foreach ($interviewedProfile[10] as $jobSector) {
                        $profile->addSector($jobSector);
                    }

                    //degree level
                    if (!empty($interviewedProfile[11])) {
                        $profile->setDegree($interviewedProfile[11]);
                    }

                    //localisation
                    if (!empty($interviewedProfile[12])) {
                        $profile->setLocality($interviewedProfile[12]);
                    }

                    //experiences
                    //TODO : add profile to timeline object
                    if ($interviewedProfile[14]['exp']['status']) {
                        $profile->setExperiencesFromExtract($interviewedProfile[14]['initial']);
                        foreach ($interviewedProfile[14]['exp']['experiences'] as $experienceTimeline) {
                            $profile->addExperience($experienceTimeline);
                        }
                    } else {
                        if (!empty($interviewedProfile[14]['exp']['logs'])) {
                            $logs = array_merge($logs, $interviewedProfile[14]['exp']['logs']);
                            //TODO : build excel log file
                        }
                    }

                    if ($profile->getId()) {
                        ++$maj;
                    }

                    if (!$profile->getId()) {
                        if (!$simulate) {
                            $this->entityManager->persist($profile);
                        }
                        ++$injected;
                    }

                    if (!$simulate) {
                        $this->entityManager->flush();
                    }

                    $sheetNameSlugged = $this->slugger->slug($sheetName);
                    $searchItwCategory = $this->entityManager->getRepository(InterviewCategory::class)
                        ->findOneBy(['nameSlugged' => $sheetNameSlugged]);
                    if (!$searchItwCategory) {
                        $searchItwCategory = (new InterviewCategory())
                            ->setName($sheetName)
                            ->setNameSlugged($sheetNameSlugged);
                    }

                    //create interview
                    $interview = new Interview();
                    $interview
                        ->setDate($interviewedProfile[0])
                        ->setProfile($profile)
                        ->setState($interviewedProfile[6])
                        ->setStateFirstInterview($interviewedProfile[15])
                        ->setStateCustomerInterviewed($interviewedProfile[16])
                        ->setConsultant($defaultConsultant)
                        ->setIsRefusedMailSent($interviewedProfile[18])
                        ->setCategory($searchItwCategory)
                    ;

                    if (!$simulate) {
                        $this->entityManager->persist($interview);
                        $this->entityManager->flush();
                    }

                    ++$interviewedCount;
                } else {
                    $logs[] = [$sheetName, $lineNumber, 'Nom et prénom ou email vide, ne peut pas être insérer'];
                }
            }
        }

        foreach ($blacklisted as $key => $black) {
            if ($key > 2) {
                $fullName = trim($black[1]);
                if (PhpSpreadsheet::cellValueIsNotEmpty($fullName)) {
                    $fullNameSlugged = $this->slugger->slug($fullName);
                    $checkProfile = $this->getRepository()->findOneBy(['fullNameSlugged' => $fullNameSlugged]);
                    $dateString = $black[0];
                    $dateStringExploded = explode(' ', $dateString);
                    if (count($dateStringExploded) > 1) {
                        $theDate = end($dateStringExploded);
                    } else {
                        $theDate = $dateStringExploded[0];
                    }
                    $checkDateBlacklist = DateTimeImmutable::createFromFormat('d/m/Y', $theDate);
                    $profile = $checkProfile ?? new Profile();
                    $profile
                        ->setFullName($fullName)
                        ->setFullNameSlugged($fullNameSlugged)
                        ->setStatus(Profile::BLACKLISTED)
                        ->setBlacklistedAt($checkDateBlacklist)
                        ->setProposedPosition($black[2])
                        ->setReasonBlacklisted($black[3])
                    ;
                    ++$blacklistedCount;

                    if ($profile->getId()) {
                        ++$maj;
                    }

                    if (!$profile->getId()) {
                        if (!$simulate) {
                            $this->entityManager->persist($profile);
                        }
                        ++$injected;
                    }

                    if (!$simulate) {
                        $this->entityManager->flush();
                    }
                }
            }
        }

        $progressBar->finish();

        if (count($logs) > 0) {
            PhpSpreadsheet::createXlsxFile(
                'Log profile_interviewed',
                $this->getLogFilePath(),
                $logsHeader,
                $logs
            );
        }

        $symfonyStyle->success(
            'Interviewed profile imported. Injected : '.$injected.' / MAJ : '.$maj.' / Blacklisted : '.
            $blacklistedCount.' / Interviewed count : '.$interviewedCount.' / Errors : '.count($logs)
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
                case 0: //last maj date
                    $explode = explode(' ', $data);
                    $lastDateValue = end($explode);
                    $checkDate = DateTimeImmutable::createFromFormat('d/m/Y', $lastDateValue);
                    if (false === $checkDate) {
                        $checkDate = DateTimeImmutable::createFromFormat('d/m/y', $lastDateValue);
                        if (false === $checkDate) {
                            $checkDate = DateTimeImmutable::createFromFormat('d/m/Y', '01/01/2019');
                        }
                    }
                    $theData = $checkDate;
                    break;
                case 1: //fullName
                    $theData = ucwords(strtolower(str_replace([':', ' :', ' :'], ['', '', ''], $data)));
                    break;
                case 2: //email
                    $theData = MailTools::extractFromString($data);
                    break;
                case 3: //phone
                    $theData = $data;
                    break;
                case 4: //years old
                    $theData = is_numeric($data) ?
                        (int) filter_var($data, FILTER_SANITIZE_NUMBER_INT) :
                        null;
                    break;
                case 5: //gender
                    $theData = Profile::recognizeGender($data);
                    break;
                case 6: //general state
                    $theData = Interview::allowToSetGeneralState($data) ? strtolower($data) : null;
                    break;
                case 7: //actually job
                    $theData = ucfirst($data);
                    break;
                case 8: //hope job title
                    $theData = ucfirst($data);
                    break;
                case 9: //year of experience
                    $dataToBuild = strtolower($data);
                    $dataToBuild = str_replace(',', '.', $dataToBuild);
                    $theData['text'] = StringTools::extractNumberOfExpFromString($dataToBuild);
                    $theData['number'] = StringTools::extractNumberOfExpFromString($dataToBuild, true);
                    break;
                case 10: //sector job
                    $theData = $this->jobSectorManager->retrieveJobSector($data);
                    break;
                case 11: //study level
                    $theData = ucfirst($data);
                    break;
                case 12: //localisation
                    $theData = ucfirst($data);
                    break;
                case 13: //expectation salary
                    $theData = ResumeTools::parseExpectationSalary($data);
                    break;
                case 14: //exp.
                    if ($lineNumber === 13) {
                        dd(ResumeTools::retrieveExperience($data, $lineNumber, $sheetName));
                    }
                    $theData['initial'] = $data;
                    $theData['exp'] = ResumeTools::retrieveExperience($data, $lineNumber, $sheetName);
                    //dd($theData['exp']);
                    break;
                case 15: //state first interview
                    $theData = Interview::allowToSetState($data) ? strtolower($data) : null;
                    break;
                case 16: //state customer interview
                    $theData = Interview::allowToSetState($data) ? strtolower($data) : null;
                    break;
                case 17: //comment
                    $theData = $data;
                    break;
                case 18: //refused email sent
                    $theData = Interview::allowToSetState($data) && 'ok' === strtolower($data);
                    break;
            }

            $buildedData[$key] = $theData;
        }

        return $buildedData;
    }

    function cleanup()
    {
        // TODO: Implement cleanup() method.
    }

    function getFilePath()
    {
        return $this->parameterBag->get('file_profile_interviewed_path');
    }

    function getLogFilePath()
    {
        return $this->parameterBag->get('filelog_profile_interviewed_path');
    }

    function getRepository()
    {
        return $this->entityManager->getRepository(Profile::class);
    }

    function cleanData(array $arrayData)
    {
        $resultData = [];
        foreach ($arrayData as $sheetName => $data) {
            $resultData[$sheetName] = [];
            foreach ($data as $line => $currentLineData) {
                $currentLineDataTmp = array_slice($currentLineData, 0, 19);
                if ($line > 0 && !empty($currentLineDataTmp[1])) {
                    $resultData[$sheetName][] = $currentLineDataTmp;
                }
            }
        }

        /*foreach ($resultData as $sheetName => $data) {
            echo $sheetName.' => '.(count($data)).' data'."\n";
        }*/
        //dd('vita');

        return $resultData;
    }

    private function getHeader()
    {
        return [
            0 => "Date du dernier MAJ",
            1 => "Nom et prénoms",
            2 => "Email",
            3 => "Numéro de Tel.",
            4 => "Ages",
            5 => "Sexe",
            6 => "Statut",
            7 => "Poste actuel",
            8 => "poste demandé",
            9 => "Année d'expérience",
            10 => "Domaine (professionnelle)",
            11 => "Niveau d'études",
            12 => "Lieu",
            13 => "pretention saliale",
            14 => "experiences",
            15 => "Resultat 1er entretien",
            16 => "résultat entretien client",
            17 => "remarques",
            18 => "Mail de refus envoyé",
        ];
    }
}
