<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilterAlias
 *
 * @ORM\Table(indexes={@ORM\Index(name="id", columns={"alias", "aliasText"})})
 * @ORM\Entity
 */
class FilterAlias
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
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="aliasText", type="string", length=255)
     */
    private $aliasText;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


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
     * Set alias
     *
     * @param string $alias
     * @return FilterAlias
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
     * Set aliasText
     *
     * @param string $aliasText
     * @return FilterAlias
     */
    public function setAliasText($aliasText)
    {
        $this->aliasText = $aliasText;

        return $this;
    }

    /**
     * Get aliasText
     *
     * @return string 
     */
    public function getAliasText()
    {
        return $this->aliasText;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FilterAlias
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
}
