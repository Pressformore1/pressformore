<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * PostType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\PostTypeRepository")
 */
class PostType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"json"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     * @Groups({"json"})
     */
    private $name;


    
    /**
     *
     * @var type string
     * @ORM\Column(name="iconColor",type="string",length=255)
     */
    private $iconColor;
    
    /**
     *
     * @var type string
     * @ORM\Column(name="iconGrey",type="string",length=255)
     */
    private $iconGrey;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    
    
    public function __construct()
    {
        $this->dateAdded = new \DateTime();
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
     * @return PostType
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
        return $this->name;        
    }

    /**
     * Set iconColor
     *
     * @param string $iconColor
     * @return PostType
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
     * @return PostType
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

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return PostType
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    
        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return PostType
     */
    public function setUser(\P4M\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}