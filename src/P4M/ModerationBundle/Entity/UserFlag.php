<?php

namespace P4M\ModerationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WallFlag
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\ModerationBundle\Entity\WallFlagRepository")
 */
class UserFlag
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
     * @ORM\Column(name="description", type="text",nullable=true)
     */
    private $description;
    
     /**
     * 
     * @ORM\OneToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="flag")
     */
    private $userTarget;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\UserFlagType")
     */
    private $type;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\ModerationBundle\Entity\UserFlagConfirmation",mappedBy="flag",cascade="remove")
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
     * @return WallFlag
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
     * Set type
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagType $type
     * @return WallFlag
     */
    public function setType(\P4M\ModerationBundle\Entity\UserFlagType $type = null)
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
     * @return WallFlag
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
     * @return WallFlag
     */
    public function addConfirmation(\P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations)
    {
        $this->confirmations[] = $confirmations;
    
        return $this;
    }

    /**
     * Remove confirmations
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagConfirmation $confirmations
     */
    public function removeConfirmation(\P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations)
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

    /**
     * Set userTarget
     *
     * @param \P4M\UserBundle\Entity\User $userTarget
     * @return UserFlag
     */
    public function setUserTarget(\P4M\UserBundle\Entity\User $userTarget = null)
    {
        $this->userTarget = $userTarget;
    
        return $this;
    }

    /**
     * Get userTarget
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getUserTarget()
    {
        return $this->userTarget;
    }
}
