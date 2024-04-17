<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as SFType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', SFType\TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Login'],
            ])
            ->add('mdp', SFType\PasswordType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Mot de passe'],
            ])
            ->add('valider', SFType\SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
