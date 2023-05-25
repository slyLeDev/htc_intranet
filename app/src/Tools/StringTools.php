<?php
/**
 * @author hR.
 */

namespace App\Tools;

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

/** StringTools */
class StringTools
{
    public static function cleanSpecialCharacter(string $text) {
        $text = trim($text);
        $utf8 = array(
            '/^[áàâãªä]/u' => 'a',
            '/^[ÁÀÂÃÄ]/u' => 'A',
            '/^[ÍÌÎÏ]/u' => 'I',
            '/^[íìîï]/u' => 'i',
            '/^[éèêë]/u' => 'e',
            '/^[ÉÈÊË]/u' => 'E',
            '/^[óòôõºö]/u' => 'o',
            '/^[ÓÒÔÕÖ]/u' => 'O',
            '/^[úùûü]/u' => 'u',
            '/^[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/[«»]/u' => '', // guillemet double
        );

        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }

    public static function containsNumber(string $text)
    {
        return preg_match('~[0-9]+~', $text);
    }

    public static function haveYearInString(string $value): bool
    {
        return ((false !== strpos(strtolower($value), 'ans')) ||
            (false !== strpos(strtolower($value), 'an')) ||
            (false !== strpos(strtolower($value), 'nz')));
    }

    public static function retrieveYearOfExp(string $value): float
    {
        $yearOfExp = 0;
        if (false !== strpos(strtolower($value), 'à')) {
            $valueExplode = explode('à', $value);
            $value = trim($valueExplode[1]);
        }

        if (self::haveYearInString($value)) {
            if (preg_match_all('/\\d+(.?\\d+)?\\s(ans|an|zn)?/', $value, $matches) > 0) {
                foreach ($matches[0] as $match) {
                    if (self::haveYearInString($match)) {
                        $yearOfExp += abs((float) trim(str_replace(['ans', 'an', 'nz', ' '], ['', '', '', ''], $match)));
                    }
                }
            }
        }

        if (false !== strpos(strtolower($value), 'demi')) {
            $yearOfExp += 0.5;
        }

        return (float) $yearOfExp;
    }

    public static function haveMonthInString(string $value): bool
    {
        return ((false !== strpos(strtolower($value), 'mois')) || (false !== strpos(strtolower($value), 'moi')));
    }

    public static function retrieveMonthOfExp(string $value)
    {
        $monthOfExp = 0;
        if (self::haveMonthInString($value)) {
            if (preg_match_all('/\\d+(.?\\d+)?\\s(mois|moi)?/', $value, $matches) > 0) {
                foreach ($matches[0] as $match) {
                    if (self::haveMonthInString($match)) {
                        $monthOfExp += abs((float) trim(str_replace(['mois', 'moi', ' '], ['', '', ''], $match)));
                    }
                }
            }

            return round($monthOfExp/12, 2);
        }

        return $monthOfExp;
    }

    public static function extractNumberOfExpFromString(string $value, ?bool $getNumber = false)
    {
        if (PhpSpreadsheet::cellValueToNotConsider($value)) {
            return null;
        }

        if (!PhpSpreadsheet::cellValueIsNotEmpty($value)) {
            return null;
        }

        if (false !== strpos(strtolower($value), 'stage')) {
            if ($getNumber) {
                return Profile::XP_YEAR_TRAINEE;
            }

            return 'Stage';
        }

        if ((false !== strpos(strtolower($value), 'moins de 2ans')) ||
            (false !== strpos(strtolower($value), 'moins de 2 ans')) ||
            (false !== strpos(strtolower($value), 'moins de 2an')) ||
            (false !== strpos(strtolower($value), 'moins de deux ans')) ||
            (false !== strpos(strtolower($value), 'moins de deux an')) ||
            (false !== strpos(strtolower($value), 'moins de 2 an'))) {
            if ($getNumber) {
                return Profile::XP_UNDER_TWO;
            }

            return '1 an';
        }

        if ((false === strpos(strtolower($value), 'mois')) && (false === strpos(strtolower($value), 'ans')) && (false === strpos(strtolower($value), 'an'))) {
            $theValue = abs((float) $value);
            if (0 === $theValue) {
                if ($getNumber) {
                    return Profile::XP_YEAR_BEGINNER;
                }

                return 'Débutant';
            }

            if ($getNumber) {
                return $theValue;
            }

            return $theValue.' an'.($theValue > 1 ? 's' : '');
        }

        $yearOfExp = self::retrieveYearOfExp($value) + self::retrieveMonthOfExp($value);
        if ($getNumber) {
            return abs($yearOfExp);
        }

        return abs($yearOfExp).' an'.(abs($yearOfExp) > 1 ? 's' : '');
    }
}
