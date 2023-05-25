<?php
/**
 * @author hR.
 */
namespace App\Interfaces;

use App\Entity\User;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** InjectDataInterface */
interface InjectDataInterface
{
    function inject(SymfonyStyle $symfonyStyle, bool $simulate);
    function cleanData(array $arrayData);
    function buildData(array $lineData, int $lineNumber, string $sheetName = '', bool $simulate = false);
    function cleanup();
    function getFilePath();
    function getRepository();
}
