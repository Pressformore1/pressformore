<?php

namespace P4M\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationType
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\NotificationBundle\Entity\NotificationTypeRepository")
 */
class NotificationType
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="typeLink",type="string",length=255)
     */
    private $typeLink;
    
    /**
     *
     * @var text
     * @ORM\Column(name="message",type="text")
     */
    private $message;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="icon",type="string",length=255)
     */
    private $icon;
    
    /**
     *
     * @var string
     * @ORM\Column(name="link",type="string",length=255)
     */
    private $link;
    
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
     * @return NotificationType
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return NotificationType
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
     * @return NotificationType
     */
    public function setUser(\P4M\UserBundle\Entity\User $user = null)
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

    /**
     * Set typeLink
     *
     * @param string $typeLink
     * @return NotificationType
     */
    public function setTypeLink($typeLink)
    {
        $this->typeLink = $typeLink;
    
        return $this;
    }

    /**
     * Get typeLink
     *
     * @return string 
     */
    public function getTypeLink()
    {
        return $this->typeLink;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return NotificationType
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return NotificationType
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    
        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return NotificationType
     */
    public function setLink($link)
    {
        $this->link = $link;
    
        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }
}
