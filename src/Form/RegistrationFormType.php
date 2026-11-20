<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 focus:bg-white transition';
        $labelClass = 'block text-sm font-semibold text-slate-700 mb-1.5';

        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex : Jean',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'Ex : Dupont',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'label_attr' => ['class' => $labelClass],
                'attr' => [
                    'class' => $inputClass,
                    'placeholder' => 'jean.dupont@exemple.fr',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_attr' => ['class' => $labelClass],
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => $inputClass,
                    'placeholder' => 'Minimum 6 caractères',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir un mot de passe.',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions générales d\'utilisation',
                'label_attr' => ['class' => 'text-sm text-slate-600 select-none cursor-pointer'],
                'attr' => [
                    'class' => 'w-4 h-4 text-sky-600 bg-slate-50 border-slate-300 rounded focus:ring-sky-500 cursor-pointer',
                ],
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter nos conditions d\'utilisation.',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

