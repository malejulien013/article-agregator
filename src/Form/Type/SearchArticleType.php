<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
class SearchArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filterType', ChoiceType::class, [
                'label' => "Filtrer par : ",
                'choices' => [
                    'Titre' => 'name',
                    'Auteur' => 'author',
                    'Date de publication' => 'publicationDate',
                    'Source' => 'source',
                ]
            ])
            ->add('filterValue', TextType::class, [
                'label' => "Valeur : ",
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => "Filtrer",
            ])
        ;
    }
}