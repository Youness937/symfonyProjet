<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type as SFType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateDebut', DateType::class, [
            // Assurez-vous que le widget est défini comme 'single_text' pour utiliser un champ de date HTML5
            'widget' => 'single_text',
            // Cela active le widget HTML5 sur les navigateurs qui le supportent
            'html5' => true,
            // Utilisez l'option 'attr' pour définir les attributs HTML directement
            'attr' => [
                // Définit l'attribut 'min' sur la date actuelle au format YYYY-MM-DD
                'min' => (new \DateTime())->format('Y-m-d'),
            ],])
            ->add('nbreHeures')
            ->add('departement')
            ->add('leproduit', EntityType::class, array(
                'class' => 'App\Entity\Produit',
                'choice_label' =>'libelle',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
