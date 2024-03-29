<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('email', EmailType::class, [
        'attr' => ['placeholder' => 'Email', 'autofocus' => true],
      ])
      ->add('firstname')
      ->add('lastname')
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
        'toggle' => true,
      ])
      ->add('country', CountryType::class, [
        'preferred_choices' => ['CH', 'DE', 'US'],
        'autocomplete' => true,
      ])
      ->add('captcha', Recaptcha3Type::class, [
        'constraints' => new Recaptcha3(),
        'action_name' => 'register',
      ])
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'help' => 'Please confirm our data protection rules',
        'label' => '<a target="_blank" href="' . $options['dataprotectURL'] . '">Link</a>',
        'label_html' => true,
        'constraints' => [
          new IsTrue([
            'message' => 'You should agree to our terms.',
          ]),
        ],
      ])
      ->add('Register', SubmitType::class, [
        'attr' => ['class' => 'btn btn-primary'],
        'row_attr' => ['class' => 'd-grid'],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'dataprotectURL' => null,
    ]);
  }
}
