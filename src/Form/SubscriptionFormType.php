<?php

namespace App\Form;

use App\Repository\AuthorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionFormType extends AbstractType
{

    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $authors = $this->authorRepository->choiceList();

        $builder
            ->add('author', ChoiceType::class,
                ['placeholder' => 'Select an author',
                  'choices' => $authors,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
