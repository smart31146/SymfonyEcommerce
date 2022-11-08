<?php

namespace OnlineShopBundle\Form;

use OnlineShopBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name", TextType::class)
            ->add("price", MoneyType::class,
                [
                    "currency" => "BGN"
                ])
            ->add("description", TextareaType::class)
            ->add("quantity", IntegerType::class)
            ->add("category", ChoiceType::class,
                [
                    "placeholder" => "Choose category"
                ])
            ->add('image_form', FileType::class, [
                'data_class' => null,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => Product::class
            ]
        );
    }


    public function getBlockPrefix()
    {
        return 'online_shop_bundle_product_type';
    }
}
