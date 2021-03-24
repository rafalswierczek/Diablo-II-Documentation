<?php declare(strict_types=1);

namespace App\Service\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{TextType, FileType, SubmitType};
use Symfony\Component\Form\FormBuilderInterface;

class DocumentationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('docName', TextType::class)
            ->add('defaultLanguage', TextType::class)
            ->add('string', FileType::class)
            ->add('expansionstring', FileType::class)
            ->add('patchstring', FileType::class)
            ->add('images', FileType::class)
            ->add('animdata', FileType::class)
            ->add('monstats', FileType::class)
            ->add('properties', FileType::class)
            ->add('itemstatcost', FileType::class)
            ->add('skills', FileType::class)
            ->add('skilldesc', FileType::class)
            ->add('misc', FileType::class)
            ->add('armor', FileType::class)
            ->add('weapons', FileType::class)
            ->add('uniqueitems', FileType::class)
            ->add('create', SubmitType::class);
    }
}