<?php

namespace OnlineShopBundle\Service;


use OnlineShopBundle\Entity\Category;
use OnlineShopBundle\Repository\PromotionRepository;

class PromotionManager
{
    protected $general_promotion;

    protected $category_promotions;

    /**
     * PriceCalculator constructor.
     *
     * @param PromotionRepository $repo
     */
    public function __construct(PromotionRepository $repo)
    {
        $this->general_promotion = $repo->fetchBiggestGeneralPromotion();
        $this->category_promotions = $repo->fetchCategoriesPromotions();
    }


    /**
     * @return int
     */
    public function getGeneralPromotion()
    {
        return $this->general_promotion ?? 0;
    }

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function hasCategoryPromotion($category)
    {
        return array_key_exists($category->getId(), $this->category_promotions);
    }

    /**
     * @param Category $category
     *
     * @return int
     */
    public function getCategoryPromotion($category)
    {
        return $this->category_promotions[$category->getId()];
    }
}
