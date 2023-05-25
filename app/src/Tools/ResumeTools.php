<?php
/**
 * @author hR.
 */

namespace App\Tools;

use App\Entity\Experiences;
use App\Entity\Profile;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use function Clue\StreamFilter\fun;

/** ResumeTools */
class ResumeTools
{
    public static function listMonth(): array
    {
        return [
            '01' =>'janvier|janver|janveir|janv.|janv|jan.|jan',
            '02' =>'février|fevrier|fervier|ferveir|fevr.|fevr|fev.|fev',
            '03' =>'mars|mras|mar.|mar',
            '04' =>'avril|arvil|avr.|arv.|avr|arv',
            '05' =>'mai|mai.|mei.|mei',
            '06' =>'juin|jiun|jnui|jniu',
            '07' =>'juillet|juill.|juill|juil|juil.|jiull|jiul|jiul.',
            '08' =>'août|aout|aûot|aou.|aoû.|aûo.|aou|aoû|aûo',
            '09' => 'séptembre|septembre|septemebre|septembr|sept.|sept',
            '10' => 'octobre|otcobre|octorbe|octobr|oct.|oct',
            '11' => 'novembre|novemember|november|nov.|nov',
            '12' => 'décembre|decembre|decemembre|december|dec.|déc.|déc|dec'
        ];
    }

    public static function fixExperienceText(string $text)
    {
        $text = preg_replace('/(De) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Depuis) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(A partir de) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(À partir de) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Janvier) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Février) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Fevrier) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Mars) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Avril) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Mai) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Juin) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Juillet) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Août) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Aout) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Septembre) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Octobre) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Novembre) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Décembre) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);
        $text = preg_replace('/(Decembre) (.*) (:)/i', "\n".'${1} ${2} ${3}', $text);

        return $text;
    }

    public static function replaceByMonthNumber(string $text)
    {
        foreach (self::listMonth() as $monthNumber => $purpose) {
            $monthPurpose = explode('|', $purpose);
            $text = str_replace($monthPurpose, $monthNumber, $text);
        }

        return $text;
    }

    /**
     * @param string $data
     *
     * @return null[]
     */
    public static function parseExpectationSalary(string $data): array
    {
        $theData = [
            'min' => null,
            'max' => null,
            'fix' => null,
        ];

        $data = str_replace(['De', 'de', 'A partir', 'à partir', 'À partir'], '', $data);

        $separatorSalary = ['à', 'a', '-', '/'];
        $hasOnOfSeparator = false;

        foreach ($separatorSalary as $separator) {
            $exploded = explode($separator, $data);
            if (count($exploded) === 2) {
                $hasOnOfSeparator = true;
                $theData['min'] = (float) str_replace(' ', '', trim($exploded[0]));
                $theData['max'] = (float) str_replace(' ', '', trim($exploded[1]));
            }
        }

        if (!$hasOnOfSeparator) {
            $theData['fix'] = (float) str_replace(' ', '', $data);
        }

        return $theData;
    }

    private static function removeLineSection(string $text)
    {
        return str_replace(["\n", "r", "n", "t"], ' ', $text);
    }

    private static function replacePointWithTwoPointAfterYear(string $text)
    {
        //$text = 'Mai2015 - Avril 2017. Consultant - PTNTIC CONGO';
        //REPLACE 'Mai2015 - Avril 2017. Consultant - PTNTIC CONGO' to 'Mai2015 - Avril 2017 : Consultant - PTNTIC CONGO'
        if (preg_match_all('/([0-9]{4})\./', $text, $matchesWrong) > 0) {//dd($matchesWrong);
            foreach ($matchesWrong[0] as $match) {
                $year = str_replace('.', '', $match);
                $text = str_replace($year.'.', $year.' : ', $text);
            }
        }

        return $text;
    }

    private static function getListOfPossibilityMonthAsString(): string
    {
        $monthAsArray = [];
        foreach (self::listMonth() as $item) {
            $monthAsArray[] = $item;
        }

        return implode('|', $monthAsArray);
    }

    private static function buildExperiencePattern($onlyTimeline = false): string
    {
        //goal : (((?:(3[01]|[12][0-9]|0?[1-9]))?(?:[\/|\.])?)?(?:(1[0-2]|0?[1-9]|(?:Jan|Janv|Fev|Mar|Avr|Avril|Mai|Jui|Juil|Aou|Sept|Oct|Octobre|Nov|Dec)))?(.*)?((?:[0-9]{4})|(.*)?)):(.*)(?:\n|\r|\t)?
        $patternMatchDay = '((?:(3[01]|[12][0-9]|0?[1-9]))?'; //d or dd
        $patternMatchMonthDaySeparator = '(?:[\/|\.])?'; // / or .
        $patternMatchDayWithSeparator = '('.$patternMatchDay.$patternMatchMonthDaySeparator.')?'; // dd/ or d/
        $listOfMonth = self::getListOfPossibilityMonthAsString();
        $patternMatchMonth = '(?:(1[0-2]|0?[1-9]|(?:'.$listOfMonth.')))?'; //m or mm
        $patternMatchMonthYearSeparator = '(.*)?'; // / or .
        $patternMatchYear = '((?:[0-9]{4})|(.*)?))'; // Y
        $patternMatchDayMonth = $patternMatchDayWithSeparator.$patternMatchMonth;
        $patternMatchDayMonthYear = $patternMatchDayMonth.$patternMatchMonthYearSeparator.$patternMatchYear;
        $patternYearTitleSeparator = ':'; // :
        $patternTitle = '(.*)(?:\n|\r|\t)?';

        if ($onlyTimeline) {
            return $patternMatchDayMonthYear;
        }

        return '/'.$patternMatchDayMonthYear.$patternYearTitleSeparator.$patternTitle.'/xi';
    }

    private static function haveYearInString(string $match, $getYear = false, $strict = false)
    {
        if ($getYear) {
            preg_match_all('/[0-9]{4}/', $match, $matches);

            return $matches[0][0] ?? '';
        }

        if ($strict) {
            return preg_match('/^[0-9]{4}$/', $match);
        }

        return preg_match('/[0-9]{4}/', $match);
    }

    private static function experienceToNow(string $period): bool
    {
        return ((false !== strpos($period, 'ce jour')) ||
            (false !== strpos($period, 'présent')) ||
            (false !== strpos($period, 'present')) ||
            (false !== strpos($period, 'maintenant')) ||
            (false !== strpos($period, 'actuel')) ||
            (false !== strpos($period, 'actuellement'))
        );
    }

    private static function isValidTimeline(string $timeline): bool
    {
        $splitPeriod = explode('-', $timeline);
        $splitPeriodA = explode('à', $timeline);
        $splitPeriodJA = explode('jusqu\'à', $timeline);
        $splitPeriodJAU = explode('jusqu\'au', $timeline);
        $haveFromText = false !== strpos($timeline, 'depuis');

        return (count($splitPeriod) === 2) ||
            (count($splitPeriodA) === 2) ||
            (count($splitPeriodJA) === 2) ||
            (count($splitPeriodJAU) === 2) ||
            (self::haveYearInString($timeline, false, true)) ||
            $haveFromText;
    }

    /**
     * @param string $text
     * @param int    $lineNumber
     * @param string $sheetName
     *
     * @return array
     */
    public static function retrieveExperience(string $text, int $lineNumber, string $sheetName): array
    {
        $textA = <<<EOF
 Novembre 2019 - ce jour : Dans une équipe de consultants en dommunication
 Chef de projet Well'Come
EOF;

        $allExperiences = [];
        $logs = [];
        if (PhpSpreadsheet::cellValueIsNotEmpty($text)) {
            $timelinesExp = explode("\n", $text);
            //dd($timelinesExp);

            //foreach timeline
            foreach ($timelinesExp as $lineTimeline => $timeline) {
                $theRealNbLineTimeline = ($lineTimeline+1);
                if (self::haveYearInString($timeline)) {
                    $timelineExploded = explode(':', $timeline);
                    if (count($timelineExploded) > 1) {
                        $period = self::replaceByMonthNumber(strtolower(trim($timelineExploded[0])));
                        $fullPosition = trim($timelineExploded[1]);
                        //dd($period, $fullPosition);
                        if (self::isValidTimeline($period) && PhpSpreadsheet::cellValueIsNotEmpty($fullPosition) && !self::haveYearInString($fullPosition)) {
                            $splitPeriod = explode('-', $period);
                            $splitPeriodA = explode('à', $period);
                            $splitPeriodJA = explode('jusqu\'à', $period);
                            $splitPeriodJAU = explode('jusqu\'au', $period);
                            $hasFrom = false !== strpos($period, 'depuis');
                            $splitPeriodBegin = explode('depuis', $period);

                            $theTimelineExp = new Experiences();
                            $theTimelineExp->setFullPosition($fullPosition);

                            if ($hasFrom) { // Depuis
                                $theTimelineExp = self::manageTimelinePeriodExploded($splitPeriodBegin, $theTimelineExp, $period, true);
                            }

                            $theTimelineExp = self::manageTimelinePeriodExploded($splitPeriod, $theTimelineExp, $period);
                            if ($lineNumber === 13) {
                                dd($theTimelineExp);
                            }
                            $theTimelineExp = self::manageTimelinePeriodExploded($splitPeriodA, $theTimelineExp, $period);
                            $theTimelineExp = self::manageTimelinePeriodExploded($splitPeriodJA, $theTimelineExp, $period);
                            $theTimelineExp = self::manageTimelinePeriodExploded($splitPeriodJAU, $theTimelineExp, $period);

                            //manage job title and company name
                            $explodeFullPosition = explode('chez', $fullPosition);
                            if (2 === count($explodeFullPosition)) {
                                $jobTitle = trim($explodeFullPosition[0]);
                                $company = trim($explodeFullPosition[1]);

                                if (PhpSpreadsheet::cellValueIsNotEmpty($jobTitle)) {
                                    $theTimelineExp->setJobTitle($jobTitle);
                                }

                                if (PhpSpreadsheet::cellValueIsNotEmpty($company)) {
                                    $theTimelineExp->setCompany($company);
                                }
                            }

                            if (!empty($theTimelineExp->getStartAt())) {
                                $allExperiences[] = $theTimelineExp;
                            } else {
                                $logs[] = [$sheetName, $lineNumber, '- Ligne '.$theRealNbLineTimeline.' : une ou des dates de début non précisée'];
                            }
                        } else {
                            $logs[] = [$sheetName, $lineNumber, '- Ligne '.$theRealNbLineTimeline.' : une ou des périodes non valide OU titre de l\'expérience vide OU l\'intitulé du poste contient une année donc possibilité de non retour à la ligne'];
                        }
                    }
                } else {
                    $logs[] = [$sheetName, $lineNumber, '- Ligne '.$theRealNbLineTimeline.' : Absence de période ou de mention d\'année(s)'];
                }
            }
        }

        if (0 === count($logs)) {
            return [
                'status' => true,
                'experiences' => $allExperiences,
                'logs' => [],
            ];
        }

        return [
            'status' => false,
            'experiences' => [],
            'logs' => $logs,
        ];
    }

    public static function getMonthYearDateFromPeriodExperience(string $period, bool $getTo = false)
    {
        //if just year like 2019 or 2020, not precise month
        if (self::haveYearInString($period, false, true)) {
            return date_create_from_format('d/m/Y', ($getTo ? '31/12' : '01/01/').$period);
        }

        return (self::haveYearInString($period, false, true) ?
            date_create_from_format('Y', $period) :
            (date_create_from_format('m Y', $period) ??
                date_create_from_format('m  Y', $period) ??
                date_create_from_format('dm Y', $period) ??
                date_create_from_format('dmY', $period) ??
                date_create_from_format('m    Y', $period) ??
                date_create_from_format('m.Y', $period) ??
                date_create_from_format('m/Y', $period)));
    }

    /**
     * @param array       $splitPeriod
     * @param Experiences $theTimelineExp
     * @param string      $period
     * @param bool        $retrieveJustBegin
     *
     * @return Experiences
     */
    private static function manageTimelinePeriodExploded(array $splitPeriod, Experiences $theTimelineExp, string $period, bool $retrieveJustBegin = false): Experiences
    {
        if (2 === count($splitPeriod)) {
            $splitPeriod[0] = trim($splitPeriod[0]);
            $splitPeriod[1] = trim($splitPeriod[1]);
            if (!self::haveYearInString($splitPeriod[0]) && self::haveYearInString($splitPeriod[1])) {
                $splitPeriod[0] = $splitPeriod[0].' '.self::haveYearInString($splitPeriod[1], true);
            }

            if ($from = self::getMonthYearDateFromPeriodExperience($splitPeriod[0])) {
                $immutableFrom = DateTimeImmutable::createFromMutable($from);
                if (!$retrieveJustBegin) {
                    $theTimelineExp->setStartAt($immutableFrom);
                }
            }

            if ($to = self::getMonthYearDateFromPeriodExperience($splitPeriod[1])) {
                $immutableTo = (false !== $to) ? DateTimeImmutable::createFromMutable($to) : null;
                if (!$retrieveJustBegin) {
                    $theTimelineExp->setEndAt($immutableTo);
                } else {
                    $theTimelineExp->setStartAt($immutableTo);
                }
            }

            if (self::experienceToNow($period)) {
                $theTimelineExp->setEndAt(null);
            }

            /*if (!$theTimelineExp->getEndAt()) {
                $theTimelineExp->setEndAt(new DateTimeImmutable());
            }*/
        } elseif (1 === count($splitPeriod)) {
            $splitPeriod[0] = trim($splitPeriod[0]);
            if (self::haveYearInString($splitPeriod[0], false, true)) {
                $from = self::getMonthYearDateFromPeriodExperience($splitPeriod[0]);
                $immutableFrom = DateTimeImmutable::createFromMutable($from);
                if (!$retrieveJustBegin) {
                    $theTimelineExp->setStartAt($immutableFrom);
                }

                $to = self::getMonthYearDateFromPeriodExperience($splitPeriod[0], true);
                $immutableTo = DateTimeImmutable::createFromMutable($from);
                if (!$retrieveJustBegin) {
                    $theTimelineExp->setEndAt($immutableTo);
                }
            }
        }

        return $theTimelineExp;
    }
}
