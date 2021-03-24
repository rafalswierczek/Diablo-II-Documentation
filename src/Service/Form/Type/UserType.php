<?php declare(strict_types=1);

namespace App\Service\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType,
    PasswordType,
    EmailType,
    TextareaType,
    SubmitType
};
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('description', TextareaType::class)
            ->add('character', TextType::class)
            ->add('login', TextType::class)
            ->add('add_date', TextType::class)
            ->add('create', SubmitType::class);
    }
}