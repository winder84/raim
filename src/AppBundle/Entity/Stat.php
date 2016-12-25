<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Stat
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Stat
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
     * @var integer
     *
     * @ORM\Column(name="productId", type="integer")
     */
    private $productId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clickDateTime", type="datetime")
     */
    private $clickDateTime;

    /**
     * @var string
     *
     * @ORM\Column(name="clientIp", type="string", length=255)
     */
    private $clientIp;

    /** @ORM\PrePersist */
    function onPrePersist()
    {
        $this->clickDateTime = new \DateTime('now');
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
     * Set productId
     *
     * @param integer $productId
     * @return Stat
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer 
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set clickDateTime
     *
     * @param \DateTime $clickDateTime
     * @return Stat
     */
    public function setClickDateTime($clickDateTime)
    {
        $this->clickDateTime = $clickDateTime;

        return $this;
    }

    /**
     * Get clickDateTime
     *
     * @return \DateTime 
     */
    public function getClickDateTime()
    {
        return $this->clickDateTime;
    }

    /**
     * Set clientIp
     *
     * @param string $clientIp
     * @return Stat
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * Get clientIp
     *
     * @return string 
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }
}
