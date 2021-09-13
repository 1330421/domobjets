<?php
//------------------------------------------
// Fichier: CustomerType.php
// Rôle: Classe modèle d'un formulaire pour un client
// Création: 2021-04-01
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, PasswordType, RepeatedType, SubmitType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class)
            ->add('firstName', TextType::class)
            ->add('name', TextType::class)
            ->add('gender', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('province', ChoiceType::class, [
                'choices' => [
                    'Provinces' => [
                        'Alberta' => 'Alberta',
                        'Colombie-Britannique' => 'Colombie-Britannique',
                        'Île-du-Prince-Édouard' => 'Île-du-Prince-Édouard',
                        'Manitoba' => 'Manitoba',
                        'Nouveau-Brunswick' => 'Nouveau-Brunswick',
                        'Nouvelle-Écosse' => 'Nouvelle-Écosse',
                        'Ontario' => 'Ontario',
                        'Québec' => 'Québec',
                        'Saskatchewan' => 'Saskatchewan',
                        'Terre-Neuve-et-Labrador' => 'Terre-Neuve-et-Labrador'
                    ],
                    'Territoires' => [
                        'Nunavut' => 'Nunavut',
                        'Territoire du Nord-Ouest' => 'Territoire du Nord-Ouest',
                        'Yukon' => 'Yukon'
                    ]
                ]
            ])
            ->add('postalCode', TextType::class)
            ->add('phone')
            ->add('email', TextType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs de mot de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            //->add('password', PasswordType::class)
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
