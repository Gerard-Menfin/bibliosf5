<?php

namespace App\Form;

use App\Entity\Abonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AbonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('roles', ChoiceType::class, [
                "choices" => [
                    "Administrateur" => "ROLE_ADMIN",
                    "Bibliothécaire" => "ROLE_BIBLIOTHECAIRE",
                    "Abonné" => "ROLE_ABONNE"
                ],
                "multiple" => true,
                "expanded" => true
            ])
            ->add('password', TextType::class, [
                /* quand l'option 'mapped' vaut false, cela signigie que l'input 'password' ne doit pas 
                    être considéré comme une propriété de l'objet Abonne => si on remplit l'input, la valeur ne sera pas
                    affectée directement à l'objet Abonne */
                "mapped" => false,
                "required" => false,
                "constraints" => [
                    new Regex([ 
                        "pattern" => "/^(?=.{6,10}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/",
                        "message" => "Le mot de passe doit comporter entre 6 et 10 caractères, une minuscule, une majuscule, un chiffre, un caractère spécial"
                    ])
                ]
            ])
            ->add('nom')
            ->add('prenom')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonne::class,
        ]);
    }
}
