<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vendor
 *
 * @ORM\Table(indexes={@ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VendorRepository")
 */
class Vendor
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Site", inversedBy="vendors", cascade={"persist"})
     * @ORM\JoinColumn(name="siteId", referencedColumnName="id")
     **/
    private $site;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    protected $media;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="vendor")
     **/
    private $products;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=true)
     */
    private $alias;

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

    /**
     *
     * @return string String Vendor
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName() : 'Новый бренд';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Vendor
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
     * @return Vendor
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
     * Set site
     *
     * @param \AppBundle\Entity\Site $site
     * @return Vendor
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
     * @return Vendor
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
     * @return Vendor
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
     * Set ourChoice
     *
     * @param boolean $ourChoice
     * @return Vendor
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
     * Set media
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     * @return Vendor
     */
    public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Vendor
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
