<?php
/**
 * @author hR.
 */

namespace App\Tools;

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

/** MailTools */
class MailTools
{
    public static function extractFromString(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace([' ', "\n", ',', 'à', '-', ':'], ['', '.', '@', '', '', ''], $string);
        $string = preg_replace('/gmail$/', 'Y', $string);
        preg_match_all("/[._a-zA-Z0-9-]+@[._a-zA-Z0-9-]+/i", $string, $matches);

        return isset($matches[0], $matches[0][0]) ? trim($matches[0][0]) : '';
    }
}
