<?php

namespace App\Form\Type;

use App\Enum\Source\SourceTypes as SourceTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
class SourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom",
            ])
            ->add('type', EnumType::class, [
                'label' => "Type de source *",
                'class' => SourceTypeEnum::class
            ])
            ->add('parameters', TextType::class, [
                'label' => "Paramètres de la source (diffère en fonction de la source)",
                'required' => false,
            ])
            ->add('attachement', FileType::class, [
                'label' => "Fichier source (si type de source 'File')",
                'required' => false,
                'mapped' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }
}