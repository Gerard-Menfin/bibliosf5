<?php

namespace App\Form;

use App\Entity\Abonne;
use App\Entity\Emprunt;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_emprunt', DateType::class, [
                "widget" => "single_text",
                "label" => "Emprunté le : "
            ])
            ->add('date_retour', DateType::class, [
                "widget" => "single_text",
                "label" => "Retourné le :",
                "required" => false
            ])
            ->add('abonne', EntityType::class, [
                "class" => Abonne::class,
                "choice_label" => "pseudo",
                "label" => "Abonné",
                "placeholder" => "Choisir un abonné..."
            ])
            ->add('livre', EntityType::class, [
                "class" => Livre::class,
                "choice_label" => function(Livre $livre){
                    return $livre->getTitre() . " - " . $livre->getAuteur();
                },
                "placeholder" => "Choisir un livre..."
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
