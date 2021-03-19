<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mot', SearchType::class, [
                "label" => "Tapez le mot à rechercher",
                "constraints" => [
                    new Length([
                        "min" => 2,
                        "minMessage" => "Veuillez taper au moins 2 caractères, svp !"
                    ]),
                    new NotBlank([
                        "message" => "Veuillez taper au moins 2 caractères, svp !"
                    ])
                ]
            ])
            ->add('rechercher', SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
