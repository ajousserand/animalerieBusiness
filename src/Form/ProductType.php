<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label',TextType::class)
            ->add('description',TextareaType::class)
            ->add('price',NumberType::class)
            ->add('stock',IntegerType::class)
            ->add('isActive', CheckboxType::class,[
                'label'=> 'Le produit est-il disponible?',
                'required'=>false
            ])
            ->add('categories', EntityType::class,[
                'class'=>Category::class,
                'query_builder' => function (CategoryRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.categoryParent IS NOT NULL');
                },
                'choice_label'=>'label',
                'multiple'=>true,
                'expanded'=>true,
                'attr'=> [
                    'class'=>'checkboxes-template'
                ]
            ])
            ->add('brand',EntityType::class,[
                'class'=>Brand::class,
                'choice_label'=>'label',
                'expanded'=>true,
                'attr'=> [
                    'class'=>'checkboxes-template'
                ]
            ])
            ->add('image',FileType::class,[
                'label'=> 'Photo de profil(jpg,png)',
                'mapped'=>false,
                // 'required'=> false,
                // 'multiple'=>true,
                // 'constraints'=>[
                //     new File([
                //         'maxSize' => '1024k',
                //         'mimeTypes' => [
                //             'image/jpeg',
                //             'image/png',
                            
                //         ],
                //         'mimeTypesMessage' => 'Uploader un bon type de fichier',
                //     ])
                // ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
