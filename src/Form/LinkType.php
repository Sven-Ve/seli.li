<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Link;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LinkType extends AbstractType
{
  public function __construct(private readonly CategoryRepository $categoryRep, private readonly TokenStorageInterface $token)
  {
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', TextType::class, [
        'attr' => ['autofocus' => true],
      ])
      ->add('url', UrlType::class, [
        'label' => 'URL',
        'default_protocol' => 'https',
      ])
      ->add('description')
      ->add('category', EntityType::class, [
        'class' => Category::class,
        'choices' => $this->categoryRep->getCategoryByUser($this->token->getToken()->getUser()),
        'help' => 'Please select the category of the link',
      ])
      ->add('favorite', null, [
        'required' => false,
        'help' => 'Is this a favorite link?',
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Link::class,
    ]);
  }
}
