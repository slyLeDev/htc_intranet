<?php
/**
 * @author hR.
 */

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Deal;
use App\Entity\Interview;
use App\Entity\InterviewCategory;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\InterviewCategoryRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use function Clue\StreamFilter\fun;

class InterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*->add('title', TextType::class, [
                'label' => 'Titre :',
                'required' => true,
            ])*/
            ->add('category', EntityType::class, [
                'label' => 'Catégorie :',
                'class' => InterviewCategory::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une catégorie',
                'multiple' => false,
                'required' => true,
            ])
            ->add('profile', EntityType::class, [
                'class' => Profile::class,
                'choice_label' => function(Profile $profile) {
                    return $profile->getFullName().($profile->getEmail() ? ' ('.$profile->getEmail().')' : '');
                },
                'multiple' => false,
                'placeholder' => 'Sélectionner le candidat',
                'required' => true,
            ])
            ->add('dateStart', TextType::class, [
                'label' => 'Date de début',
                'required' => true,
            ])
            ->add('dateEnd', TextType::class, [
                'label' => 'Date de fin',
                'required' => true,
            ])
            ->add('hourStart', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('hourEnd', TextType::class, [
                'mapped' => false,
                'required' => true,
            ])
        ;

        $builder
            ->get('dateStart')
            ->addModelTransformer(new CallbackTransformer(
                function (?DateTime $dateStart) {
                    return $dateStart ? $dateStart->format('d/m/Y') : null;
                },
                function (?string $dateStart) {
                    return $dateStart ? DateTimeImmutable::createFromFormat('d/m/Y', $dateStart) : null;
                }
            ));

        $builder
            ->get('dateEnd')
            ->addModelTransformer(new CallbackTransformer(
                function (?DateTime $dateEnd) {
                    return $dateEnd ? $dateEnd->format('d/m/Y') : null;
                },
                function (?string $dateEnd) {
                    return $dateEnd ? DateTimeImmutable::createFromFormat('d/m/Y', $dateEnd) : null;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);
    }
}
