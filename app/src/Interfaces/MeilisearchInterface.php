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

/** MeilisearchInterface */
interface MeilisearchInterface
{
    function getEntity(): string;
    function doSearch(string $searchTerm, array $searchParams = []): array;
    function doRawSearch(string $searchTerm, array $searchParams = []): array;
}
