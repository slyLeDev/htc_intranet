<?php
/**
 * @author hR.
 */

namespace App\Form;

use App\Entity\Customer;
use App\Entity\JobSector;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('manager', TextType::class, [
                'required' => true,
            ])
            ->add('interlocutor', TextType::class, [
                'required' => true,
            ])
            ->add('locality')
            ->add('phone')
            ->add('website')
            ->add('logoFile', FileType::class, [
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Seule les images sont accéptés',
                    ])
                ],
            ])
            ->add('sector', EntityType::class, [
                'class' => JobSector::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
