<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('subscriptions')
            ->add('password', PasswordType::class)
            ->add('passwordConfirm', PasswordType::class)
        ;
        if (in_array('admin_edit', $options['validation_groups'])) {
            $builder
                ->remove('email')
                ->add('email', EmailType::class, ['disabled' => true])
                ->remove('password')
                ->remove('passwordConfirm');
        }
        if (in_array('user_edit', $options['validation_groups'])) {
            $builder
                ->remove('password')
                ->remove('passwordConfirm');
        }
        if (in_array('registration', $options['validation_groups'])) {
            $builder
                ->remove('subscriptions');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['default', 'admin_edit', 'admin_create', 'user_edit', 'registration'],
        ]);
    }
}
