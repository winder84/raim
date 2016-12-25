<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(indexes={@ORM\Index(name="externalId", columns={"externalId"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProductRepository")
 */
class Product
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(name="siteId", referencedColumnName="id")
     **/
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ExternalCategory", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id", onDelete="SET NULL")
     **/
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Vendor", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(name="vendorId", referencedColumnName="id", onDelete="SET NULL")
     **/
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendorCode", type="string", length=255, nullable=true)
     */
    private $vendorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="externalId", type="string", length=255, nullable=true)
     */
    private $externalId;

    /**
     * @var string
     *
     * @ORM\Column(name="currencyId", type="string", length=255, nullable=true)
     */
    private $currencyId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255, nullable=true)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="modifiedTime", type="string", length=255, nullable=true)
     */
    private $modifiedTime;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="oldPrice", type="float", nullable=true)
     */
    private $oldPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="typePrefix", type="string", length=255, nullable=true)
     */
    private $typePrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float", nullable=true)
     */
    private $version;

    /**
     * @var json_encode
     *
     * @ORM\Column(name="pictures", type="json_array", nullable=true)
     */
    private $pictures;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ourChoice", type="boolean", nullable=true)
     */
    private $ourChoice;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean", nullable=true, options={"default" = false})
     */
    private $isDelete = false;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @ORM\ManyToMany(targetEntity="ProductPropertyValue", mappedBy="products", cascade={"persist", "remove"}, indexBy="productpropertyvalue_id, product_id")
     */
    private $productPropertyValues;

    /**
     *
     * @return string String Product
     */
    public function __toString()
    {
        return $this->getModel() ? $this->getName() . $this->getModel() : 'Новый продукт';
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
     * @return Product
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
     * Set currencyId
     *
     * @param string $currencyId
     * @return Product
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return string
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return Product
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set modifiedTime
     *
     * @param string $modifiedTime
     * @return Product
     */
    public function setModifiedTime($modifiedTime)
    {
        $this->modifiedTime = $modifiedTime;

        return $this;
    }

    /**
     * Get modifiedTime
     *
     * @return string
     */
    public function getModifiedTime()
    {
        return $this->modifiedTime;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
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
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set typePrefix
     *
     * @param string $typePrefix
     * @return Product
     */
    public function setTypePrefix($typePrefix)
    {
        $this->typePrefix = $typePrefix;

        return $this;
    }

    /**
     * Get typePrefix
     *
     * @return string
     */
    public function getTypePrefix()
    {
        return $this->typePrefix;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Product
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set version
     *
     * @param float $version
     * @return Product
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
     * Set pictures
     *
     * @param array $pictures
     * @return Product
     */
    public function setPictures($pictures)
    {
        $this->pictures = $pictures;

        return $this;
    }

    /**
     * Get pictures
     *
     * @return array
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     * @return Product
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
     * Set category
     *
     * @param \AppBundle\Entity\ExternalCategory $category
     * @return Product
     */
    public function setCategory(\AppBundle\Entity\ExternalCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\ExternalCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set vendor
     *
     * @param \AppBundle\Entity\Vendor $vendor
     * @return Product
     */
    public function setVendor(\AppBundle\Entity\Vendor $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \AppBundle\Entity\Vendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set ourChoice
     *
     * @param boolean $ourChoice
     * @return Product
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Product
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set isDelete
     *
     * @param boolean $isDelete
     * @return Product
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;

        return $this;
    }

    /**
     * Get isDelete
     *
     * @return boolean
     */
    public function getIsDelete()
    {
        return $this->isDelete;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Product
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
     * Constructor
     */
    public function __construct()
    {
        $this->productPropertyValues = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add productPropertyValues
     *
     * @param \AppBundle\Entity\ProductPropertyValue $productPropertyValues
     * @return Product
     */
    public function addProductPropertyValue(\AppBundle\Entity\ProductPropertyValue $productPropertyValues)
    {
        $this->productPropertyValues[] = $productPropertyValues;

        return $this;
    }

    /**
     * Remove productPropertyValues
     *
     * @param \AppBundle\Entity\ProductPropertyValue $productPropertyValues
     */
    public function removeProductPropertyValue(\AppBundle\Entity\ProductPropertyValue $productPropertyValues)
    {
        $this->productPropertyValues->removeElement($productPropertyValues);
    }

    /**
     * Get productPropertyValues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductPropertyValues()
    {
        return $this->productPropertyValues;
    }

    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     * @return Product
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode
     *
     * @return string 
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * Set oldPrice
     *
     * @param float $oldPrice
     * @return Product
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    /**
     * Get oldPrice
     *
     * @return float 
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }
}
