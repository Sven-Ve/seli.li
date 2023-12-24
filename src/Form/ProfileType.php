<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('firstName', null, [
        'attr' => ['autofocus' => true],
        'help' => 'Visible name',
      ])
      ->add('lastName')
      ->add('country', CountryType::class, [
        'preferred_choices' => ['CH', 'DE', 'US'],
        'attr' => [
          'class' => 'selectpicker',
          'data-live-search' => 'true',
        ],
      ])
      ->add('captcha', Recaptcha3Type::class, [
        'constraints' => new Recaptcha3(),
        'action_name' => 'profile',
      ])
      ->add('Save', SubmitType::class, ['attr' => ['class' => 'btn btn-primary btn-block']]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
