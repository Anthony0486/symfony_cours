<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'label_attr' => [
                    'class' => 'block text-sm font-semibold text-slate-700 mb-1.5',
                ],
                'attr' => [
                    'class' => 'w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:bg-white transition',
                    'placeholder' => 'Ex : Science-Fiction, Roman, Thriller...',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['is_edit'] ? 'Modifier la catégorie' : 'Ajouter une nouvelle catégorie',
                'attr' => [
                    'class' => 'w-full py-3 px-6 bg-sky-600 text-white font-semibold rounded-xl shadow-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition cursor-pointer',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'is_edit' => false,
        ]);
    }
}

