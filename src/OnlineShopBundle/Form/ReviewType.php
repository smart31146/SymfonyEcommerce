<?php

namespace OnlineShopBundle\Form;

use OnlineShopBundle\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("comment", TextType::class)
            ->add("rating", ChoiceType::class, [
                "choices" => [
                    1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => Review::class
            ]
        );
    }

}
