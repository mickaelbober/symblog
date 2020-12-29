<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'help' => 'Le titre doit faire au moins 10 caractères.',
                'attr' => [
                    'placeholder' => 'Titre de l\'article'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'help' => 'Le contenu doit faire au moins 10 caractères.',
                'attr' => [
                    'placeholder' => 'Contenu de l\'article',
                    'rows' => '5'
                ]
            ])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'help' => 'L\'url doit être au format http ou https.',
                'attr' => [
                    'placeholder' => 'URL de l\'image'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'help' => 'Sélectionner une catégorie.',
                'label' => 'Catégorie',
                'attr' => [
                    'class' => 'custom-select',
                    'placeholder' => 'Catégorie de l\'article'
                ]
            ]);        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'attr' => [
                'id' => 'form-article'
            ]
        ]);
    }
}
