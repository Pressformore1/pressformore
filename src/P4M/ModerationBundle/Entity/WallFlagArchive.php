<?php

namespace P4M\ModerationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WallFlagArchive
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\ModerationBundle\Entity\WallFlagArchiveRepository")
 */
class WallFlagArchive
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     * 
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Wall", inversedBy="flag")
     */
    private $wall;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\WallFlagType")
     */
    private $type;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\ModerationBundle\Entity\WallFlagConfirmation",mappedBy="flagArchive",cascade="persist")
     */
    private $confirmations;
    


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
     * Set description
     *
     * @param string $description
     * @return WallFlagArchive
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
     * Constructor
     */
    public function __construct()
    {
        $this->confirmations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set wall
     *
     * @param \P4M\CoreBundle\Entity\Wall $wall
     * @return WallFlagArchive
     */
    public function setWall(\P4M\CoreBundle\Entity\Wall $wall = null)
    {
        $this->wall = $wall;
    
        return $this;
    }

    /**
     * Get wall
     *
     * @return \P4M\CoreBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
    }

    /**
     * Set type
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagType $type
     * @return WallFlagArchive
     */
    public function setType(\P4M\ModerationBundle\Entity\WallFlagType $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \P4M\ModerationBundle\Entity\WallFlagType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WallFlagArchive
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
     * Add confirmations
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagConfirmation $confirmations
     * @return WallFlagArchive
     */
    public function addConfirmation(\P4M\ModerationBundle\Entity\WallFlagConfirmation $confirmations)
    {
        $this->confirmations[] = $confirmations;
    
        return $this;
    }

    /**
     * Remove confirmations
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagConfirmation $confirmations
     */
    public function removeConfirmation(\P4M\ModerationBundle\Entity\WallFlagConfirmation $confirmations)
    {
        $this->confirmations->removeElement($confirmations);
    }

    /**
     * Get confirmations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfirmations()
    {
        return $this->confirmations;
    }
}
