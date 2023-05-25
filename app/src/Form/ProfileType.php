<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('receivedAt',  DateType::class, [
                'label' => 'Candidature reçu le',
                'required' => true,
            ])
            ->add('fullName',  TextType::class, [
                'label' => 'Nom et prénom',
                'required' => true,
            ])
            ->add('email',  EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('yearsOld')
            ->add('gender')
            ->add('actuallyJobTitle', TextType::class, [
                'label' => 'Poste actuel',
                'required' => true,
            ])
            ->add('hopeJobTitle')
            ->add('yearOfExperience')
            ->add('timelineExperience')
            ->add('degree')
            ->add('locality', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('fullAddress', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('currentSalary')
            ->add('salaryExpectationMin')
            ->add('salaryExpectationMax')
            ->add('currentState')
            ->add('comment', TextareaType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('source')
            ->add('curriculumVitae')
            ->add('profilePhoto')
            ->add('sectors')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
