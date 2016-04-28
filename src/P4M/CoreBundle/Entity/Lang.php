<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Lang
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\LangRepository")
 */
class Lang
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("list")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,unique=true)
     * @Groups("list")
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nativeName", type="string", length=255,unique=true,nullable=true)
     * @Groups("list")
     */
    private $nativeName;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255,unique=true)
     *
     */
    private $code;
    
    /**
     *
     * @var type string
     * @ORM\Column(name="iconColor",type="string",length=255)
     * @Groups("list")
     */
    private $iconColor;
    
    /**
     *
     * @var type string
     * @ORM\Column(name="iconGrey",type="string",length=255)
     * @Groups("json")
     */
    private $iconGrey;
    
    
    public function __construct()
    {
        $this->iconColor ="";
        $this->iconGrey ="";
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
     * @return Lang
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
    
    
    public function __toString()
    {
        return $this->code;
    }

    /**
     * Set nativeName
     *
     * @param string $nativeName
     * @return Lang
     */
    public function setNativeName($nativeName)
    {
        $this->nativeName = $nativeName;
    
        return $this;
    }

    /**
     * Get nativeName
     *
     * @return string 
     */
    public function getNativeName()
    {
        return $this->nativeName;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Lang
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set iconColor
     *
     * @param string $iconColor
     * @return Lang
     */
    public function setIconColor($iconColor)
    {
        $this->iconColor = $iconColor;
    
        return $this;
    }

    /**
     * Get iconColor
     *
     * @return string 
     */
    public function getIconColor()
    {
        return $this->iconColor;
    }

    /**
     * Set iconGrey
     *
     * @param string $iconGrey
     * @return Lang
     */
    public function setIconGrey($iconGrey)
    {
        $this->iconGrey = $iconGrey;
    
        return $this;
    }

    /**
     * Get iconGrey
     *
     * @return string 
     */
    public function getIconGrey()
    {
        return $this->iconGrey;
    }
}