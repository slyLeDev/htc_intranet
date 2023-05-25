<?php
/**
 * @author hR.
 */

namespace App\Tools;

use App\Entity\Profile;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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

/** DateTools */
class DateTools
{
    public static function getCountMonth(DateTimeImmutable $startAt, ?DateTimeImmutable $endDate = null): int
    {
        $endDate = $endDate ?? new DateTime('now');
        $diff = abs(strtotime($endDate) - strtotime($startAt));
        $years = floor($diff / (365*60*60*24));

        return (int) floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    }

    public static function getTotalTimeline(float $months, $getXpYearNumber = false)
    {
        $year = floor($months / 12);
        $month = $months % 12;

        if ($getXpYearNumber) {
            return round($months / 12, 2);
        }

        if ((int) $year === 0 && $month === 0) {
            return 'Pas d\'expérience';
        }

        if ((int) $year === 0) {
            return $month.' mois';
        }

        if ((int) $year > 0) {
            return $year.' an'.($year > 1 ? 's' : '').($month > 0 ? ' et '. $month. ' mois' : '');
        }

        return 'Erreur';
    }
}
