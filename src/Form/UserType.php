<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('email', EmailType::class, [
        'attr' => ['autofocus' => true],
      ])
      ->add('firstName')
      ->add('lastName')
      ->add('Roles', ChoiceType::class, [
        'required' => true,
        'multiple' => true,
        'expanded' => true,
        'choices' => [
          'User' => 'ROLE_USER',
          'Admin' => 'ROLE_ADMIN',
          'Super Admin' => 'ROLE_SUPER_ADMIN',
        ],
      ])
      ->add('country', CountryType::class, [
        'preferred_choices' => ['CH', 'DE', 'US'],
        'autocomplete' => true,
      ])
      ->add('isBlocked')
      ->add('blockReason')
      ->add('isVerified');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
