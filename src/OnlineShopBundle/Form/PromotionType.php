<?php

namespace OnlineShopBundle\Form;

use OnlineShopBundle\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("percent", NumberType::class)
            ->add("startDate", null)
            ->add("endDate", null)
            ->add("category", null,
                [
                    "placeholder" => "All categories"
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => Promotion::class
            ]
        );
    }
}
