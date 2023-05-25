<?php
/**
 * @author hR.
 */
namespace App\Interfaces;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** ViewablePdfFile */
interface ViewablePdfFileInterface
{
    public const EXTENSION_TO_REPLACE = ['docx', 'doc', 'xlsx', 'xls'];

    public function isViewableFile(string $targetPath);

    public function getViewableFilename();
}
