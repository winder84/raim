<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SiteRepository")
 */
class Site
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="site")
     **/
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vendor", mappedBy="site")
     **/
    private $vendors;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ExternalCategory", mappedBy="site")
     **/
    private $externalCategories;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="seoDescription", type="text", nullable=true)
     */
    private $seoDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="seoKeywords", type="text", nullable=true)
     */
    private $seoKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="xmlParseUrl", type="string", length=255)
     */
    private $xmlParseUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryUrl", type="string", length=255, nullable=true)
     */
    private $deliveryUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentUrl", type="string", length=255, nullable=true)
     */
    private $paymentUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="logoUrl", type="string", length=255, nullable=true)
     */
    private $logoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @ORM\Column(name="lastParseDate", type="datetime", nullable=true)
     */
    protected $lastParseDate;

    /**
     * @ORM\Column(name="updatePeriod", type="integer", nullable=true)
     */
    protected $updatePeriod;

    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float", nullable=true)
     */
    private $version;

    public function __construct() {
        $this->externalCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vendors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     *
     * @return string String Site
     */
    public function __toString()
    {
        return $this->getTitle() ? $this->getTitle() : 'Новый магазин';
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
     * Set title
     *
     * @param string $title
     * @return Site
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Site
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
     * Set seoDescription
     *
     * @param string $seoDescription
     * @return Site
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * Get seoDescription
     *
     * @return string 
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * Set seoKeywords
     *
     * @param string $seoKeywords
     * @return Site
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    /**
     * Get seoKeywords
     *
     * @return string 
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * Set xmlParseUrl
     *
     * @param string $xmlParseUrl
     * @return Site
     */
    public function setXmlParseUrl($xmlParseUrl)
    {
        $this->xmlParseUrl = $xmlParseUrl;

        return $this;
    }

    /**
     * Get xmlParseUrl
     *
     * @return string 
     */
    public function getXmlParseUrl()
    {
        return $this->xmlParseUrl;
    }

    /**
     * Set deliveryUrl
     *
     * @param string $deliveryUrl
     * @return Site
     */
    public function setDeliveryUrl($deliveryUrl)
    {
        $this->deliveryUrl = $deliveryUrl;

        return $this;
    }

    /**
     * Get deliveryUrl
     *
     * @return string 
     */
    public function getDeliveryUrl()
    {
        return $this->deliveryUrl;
    }

    /**
     * Set paymentUrl
     *
     * @param string $paymentUrl
     * @return Site
     */
    public function setPaymentUrl($paymentUrl)
    {
        $this->paymentUrl = $paymentUrl;

        return $this;
    }

    /**
     * Get paymentUrl
     *
     * @return string 
     */
    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Site
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
     * Set alias
     *
     * @param string $alias
     * @return Site
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
     * Set lastParseDate
     *
     * @param \DateTime $lastParseDate
     * @return Site
     */
    public function setLastParseDate($lastParseDate)
    {
        $this->lastParseDate = $lastParseDate;

        return $this;
    }

    /**
     * Get lastParseDate
     *
     * @return \DateTime 
     */
    public function getLastParseDate()
    {
        return $this->lastParseDate;
    }

    /**
     * Set updatePeriod
     *
     * @param integer $updatePeriod
     * @return Site
     */
    public function setUpdatePeriod($updatePeriod)
    {
        $this->updatePeriod = $updatePeriod;

        return $this;
    }

    /**
     * Get updatePeriod
     *
     * @return integer 
     */
    public function getUpdatePeriod()
    {
        return $this->updatePeriod;
    }

    /**
     * Set version
     *
     * @param float $version
     * @return Site
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
     * Add externalCategories
     *
     * @param \AppBundle\Entity\ExternalCategory $externalCategories
     * @return Site
     */
    public function addExternalCategory(\AppBundle\Entity\ExternalCategory $externalCategories)
    {
        $this->externalCategories[] = $externalCategories;

        return $this;
    }

    /**
     * Remove externalCategories
     *
     * @param \AppBundle\Entity\ExternalCategory $externalCategories
     */
    public function removeExternalCategory(\AppBundle\Entity\ExternalCategory $externalCategories)
    {
        $this->externalCategories->removeElement($externalCategories);
    }

    /**
     * Get externalCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExternalCategories()
    {
        return $this->externalCategories;
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product $products
     * @return Site
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
     * Add vendors
     *
     * @param \AppBundle\Entity\Vendor $vendors
     * @return Site
     */
    public function addVendor(\AppBundle\Entity\Vendor $vendors)
    {
        $this->vendors[] = $vendors;

        return $this;
    }

    /**
     * Remove vendors
     *
     * @param \AppBundle\Entity\Vendor $vendors
     */
    public function removeVendor(\AppBundle\Entity\Vendor $vendors)
    {
        $this->vendors->removeElement($vendors);
    }

    /**
     * Get vendors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * Set logoUrl
     *
     * @param string $logoUrl
     * @return Site
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    /**
     * Get logoUrl
     *
     * @return string 
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }
}
