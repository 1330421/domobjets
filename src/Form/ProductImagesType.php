<?php
//------------------------------------------
// Fichier: ProductImagesType.php
// Rôle: Classe modèle d'un formulaire pour les images d'un produit
// Création: 2021-05-05
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Form;

use App\Classe\ProductImages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{FileType, SubmitType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productImage', FileType::class, [
                'required' => false
            ])
            ->add('descriptionImage', FileType::class, [
                'required' => false
            ])
            ->add('Envoyer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductImages::class,
        ]);
    }
}
