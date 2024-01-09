<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
    ->add('plainPassword', PasswordType::class, [
      'label' => 'Password',
      'help' => 'Your password should be at least 8 characters',
      'mapped' => false,
      'constraints' => [
        new NotBlank([
          'message' => 'Please enter a password',
        ]),
        new Length([
          'min' => 8,
          'minMessage' => 'Your password should be at least {{ limit }} characters',
          // max length allowed by Symfony for security reasons
          'max' => 4096,
        ]),
      ],
      'toggle' => true
    ]);
}

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([]);
  }
}
