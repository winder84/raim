<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductPropertyValue
 *
 * @ORM\Table(indexes={@ORM\Index(name="id", columns={"productPropertyId", "value"})})
 * @ORM\Entity
 */
class ProductPropertyValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ProductProperty", inversedBy="values", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="productPropertyId", referencedColumnName="id")
     */
    private $productProperty;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="productPropertyValues", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="ProductPropertyValuesLink")
     */
    private $products;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean", nullable=true, options={"default" = true})
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="propValue", type="string", length=255, nullable=true)
     */
    private $propValue;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return ProductPropertyValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return ProductPropertyValue
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set productProperty
     *
     * @param \AppBundle\Entity\productProperty $productProperty
     * @return ProductPropertyValue
     */
    public function setProductProperty(\AppBundle\Entity\productProperty $productProperty = null)
    {
        $this->productProperty = $productProperty;

        return $this;
    }

    /**
     * Get productProperty
     *
     * @return \AppBundle\Entity\productProperty 
     */
    public function getProductProperty()
    {
        return $this->productProperty;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product $products
     * @return ProductPropertyValue
     */
    public function addProduct(\AppBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \AppBundle\Entity\Product $products
     */
    public function removeProduct(\AppBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return ProductPropertyValue
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set propValue
     *
     * @param string $propValue
     * @return ProductPropertyValue
     */
    public function setPropValue($propValue)
    {
        $this->propValue = $propValue;

        return $this;
    }

    /**
     * Get propValue
     *
     * @return string 
     */
    public function getPropValue()
    {
        return $this->propValue;
    }
}
