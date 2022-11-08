<?php

namespace OnlineShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category
 * @UniqueEntity("name")
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="OnlineShopBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Product[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="OnlineShopBundle\Entity\Product", mappedBy="category", cascade={"persist"})
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="OnlineShopBundle\Entity\Promotion",mappedBy="category")
     */
    private $promotions;

    public function __construct()
    {
        $this->product = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|Product[]
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ArrayCollection|Product[] $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param mixed $promotions
     */
    public function setPromotions($promotions)
    {
        $this->promotions = $promotions;
    }


}

