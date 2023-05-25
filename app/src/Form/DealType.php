<?php
/**
 * @author hR.
 */

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Deal;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'onkeydown' => 'return event.keyCode !== 188 && event.keyCode !== 190',
                ],
                'required' => true,
            ])
            ->add('jobName', TextType::class, [
                'label' => 'Intitulé du poste',
                'required' => true,
            ])
            ->add('jobDescription', TextareaType::class, [
                'label' => 'Fiche de poste',
                'required' => false,
            ])
            ->add('deadline', TextType::class, [
                'label' => 'Deadline',
                'required' => true,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Remarque(s)',
                'required' => false,
            ])
            ->add('salaryMin', IntegerType::class, [
                'attr' => [
                    'onkeydown' => 'return event.keyCode !== 188 && event.keyCode !== 190',
                ],
                'required' => false,
            ])
            ->add('salaryMax', IntegerType::class, [
                'attr' => [
                    'onkeydown' => 'return event.keyCode !== 188 && event.keyCode !== 190',
                ],
                'required' => false,
            ])
            ->add('salaryState', ChoiceType::class, [
                'choices' => [
                    'Salaire variable' => Deal::SALARY_VARIABLE,
                    'Salaire exacte' => Deal::SALARY_EXACT,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('salaryExact', IntegerType::class, [
                'attr' => [
                    'onkeydown' => 'return event.keyCode !== 188 && event.keyCode !== 190',
                ],
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => Deal::STATUS_PENDING,
                    'Suspendu' => Deal::STATUS_INTERRUPTED,
                    'Cloturé' => Deal::STATUS_CLOSE,
                ],
                'required' => true,
            ])
            ->add('responsibleConsultant', EntityType::class, [
                'label' => 'Responsable(s) (un ou plusieurs consultant) :',
                'class' => User::class,
                'choice_label' => 'fullName',
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository->findByRole(User::ROLE_CONSULTANT, false);
                },
                'multiple' => true,
                'required' => true,
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'multiple' => false,
                'placeholder' => 'Sélectionner le client',
                'required' => true,
            ])
            ->add('dealFile', FileType::class, [
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'label' => 'Fichier du contrat',
                'attr' => [
                    'accept' => '.xlsx,.xls,.doc,.docx,.pdf',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/*',
                        ],
                        'mimeTypesMessage' => 'Seule les documents au format PDF et Word sont accéptés',
                    ])
                ],
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'required' => true,
            ])
        ;

        $builder->get('deadline')
            ->addModelTransformer(new CallbackTransformer(
                function (?DateTime $deadline) {
                    return $deadline ? $deadline->format('d/m/Y') : null;
                },
                function (?string $deadline) {
                    return $deadline ? DateTime::createFromFormat('d/m/Y', $deadline) : null;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deal::class,
        ]);
    }
}
