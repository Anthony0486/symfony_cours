<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:bg-white transition';
        $labelClass = 'block text-sm font-semibold text-slate-700 mb-1.5';

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex : Dune',
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'required' => false,
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex : Frank Herbert',
                ],
            ])
            ->add('publishAt', DateType::class, [
                'label' => 'Date de publication',
                'required' => false,
                'widget' => 'single_text',
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                ],
            ])
            ->add('resume', TextareaType::class, [
                'label' => 'Résumé',
                'required' => false,
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'rows' => 4,
                    'placeholder' => 'Résumé de l\'ouvrage...',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégories',
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer le livre',
                'attr' => [
                    'class' => 'w-full py-3 px-6 bg-sky-600 text-white font-semibold rounded-xl shadow-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition cursor-pointer',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

