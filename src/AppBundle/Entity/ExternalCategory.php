<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalCategory
 *
 * @ORM\Table(indexes={@ORM\Index(name="externalId", columns={"externalId"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ExternalCategoryRepository")
 */
class ExternalCategory
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
     * @ORM\Column(name="externalId", type="string", length=255, nullable=true)
     */
    private $externalId;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="externalCategories", cascade={"persist"})
     * @ORM\JoinColumn(name="internalParentId", referencedColumnName="id")
     **/
    private $internalParentCategory;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site", inversedBy="externalCategories", cascade={"persist"})
     * @ORM\JoinColumn(name="siteId", referencedColumnName="id")
     **/
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="category")
     **/
    private $products;

    /**
     * @var string
     *
     * @ORM\Column(name="parentId", type="string", length=255, nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float", nullable=true)
     */
    private $version;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ourChoice", type="boolean", nullable=true)
     */
    private $ourChoice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean", nullable=true, options={"default" = true})
     */
    private $isActive = true;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ? $this->getName() : 'Новая категория магазина';
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
     * Set externalId
     *
     * @param string $externalId
     * @return ExternalCategory
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * Get externalId
     *
     * @return string 
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set parentId
     *
     * @param string $parentId
     * @return ExternalCategory
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return string 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ExternalCategory
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
     * Set version
     *
     * @param float $version
     * @return ExternalCategory
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return float 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set internalParentCategory
     *
     * @param \AppBundle\Entity\Category $internalParentCategory
     * @return ExternalCategory
     */
    public function setInternalParentCategory(\AppBundle\Entity\Category $internalParentCategory = null)
    {
        $this->internalParentCategory = $internalParentCategory;

        return $this;
    }

    /**
     * Get internalParentCategory
     *
     * @return \AppBundle\Entity\Category 
     */
    public function getInternalParentCategory()
    {
        return $this->internalParentCategory;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     * @return ExternalCategory
     */
    public function setSite(\AppBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \AppBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product $products
     * @return ExternalCategory
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
     * Set ourChoice
     *
     * @param boolean $ourChoice
     * @return ExternalCategory
     */
    public function setOurChoice($ourChoice)
    {
        $this->ourChoice = $ourChoice;

        return $this;
    }

    /**
     * Get ourChoice
     *
     * @return boolean 
     */
    public function getOurChoice()
    {
        return $this->ourChoice;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return ExternalCategory
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
}
