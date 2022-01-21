<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\Type\PriceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Tapez le nom du produit'],
                'required' => false,
                // 'constraints' => new NotBlank(['message' => "Validation du formulaire : le nom du produit ne peut pas être vide"])
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Tapez une description courte mais parlante pour le visiteur']
            ])
            ->add('price', PriceType::class, [
                'label' => 'Prix du produit',
                'attr' => ['placeholder' => 'Tapez le prix du produit en €'],
                'required' => false,
                // 'constraints' => new NotBlank(['message' => 'Le prix du produit est obligatoire'])
            ])
            ->add('picture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Entrez le lien de l\'image']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function (category $category) {
                    return strtoupper($category->getName());
                }
            ]);

        // $builder->get('price')->addModelTransformer(new CentimesTransformer);
        
        // // On fait l'affichage du prix dans le form modifier en € (on divise par 100)
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
        //     $form = $event->getForm();

        //     /** @var Product */
        //     $product = $event->getData();

        //     if ($product !== null) {
        //         $product->setPrice($product->getPrice() / 100);
        //     }


        // //     if ($product === null) {
        // //         $form->add('category', EntityType::class, [
        // //             'label' => 'Catégorie',
        // //             'placeholder' => '-- choisir une catégorie --',
        // //             'class' => Category::class,
        // //             'choice_label' => function (category $category) {
        // //                 return strtoupper($category->getName());
        // //             }
        // //         ]);
        // //     }
        // // });
        // });

        // // On convertie le prix en centime lors d'un ajout (x100)
        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
        //     $product = $event->getData();

        //     if ($product !== null) {
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Attribut qui permet au form de prendre la classe produit
            'data_class' => Product::class,
        ]);
    }
}
