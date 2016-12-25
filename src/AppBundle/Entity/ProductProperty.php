<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * ProductProperty
 *
 * @ORM\Table(indexes={@ORM\Index(name="id", columns={"name"})})
 * @ORM\Entity
 */
class ProductProperty
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="showName", type="string", length=255, nullable=true)
     */
    private $showName;

    /**
     * @ORM\OneToMany(targetEntity="ProductPropertyValue", mappedBy="productProperty")
     */
    private $values;

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
     * Constructor
     */
    public function __construct()
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return ProductProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return ProductProperty
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
     * Add values
     *
     * @param \AppBundle\Entity\ProductPropertyValue $values
     * @return ProductProperty
     */
    public function addValue(\AppBundle\Entity\ProductPropertyValue $values)
    {
        $this->values[] = $values;

        return $this;
    }

    /**
     * Remove values
     *
     * @param \AppBundle\Entity\ProductPropertyValue $values
     */
    public function removeValue(\AppBundle\Entity\ProductPropertyValue $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set showName
     *
     * @param string $showName
     * @return ProductProperty
     */
    public function setShowName($showName)
    {
        $this->showName = $showName;

        return $this;
    }

    /**
     * Get showName
     *
     * @return string 
     */
    public function getShowName()
    {
        return $this->showName;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return ProductProperty
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
}
