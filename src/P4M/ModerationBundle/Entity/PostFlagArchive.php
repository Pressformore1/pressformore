<?php

namespace P4M\ModerationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostFlag
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\ModerationBundle\Entity\PostFlagRepository")
 */
class PostFlagArchive
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
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Post", inversedBy="flag")
     */
    private $post;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\FlagType")
     */
    private $type;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\ModerationBundle\Entity\FlagConfirmation",mappedBy="flagArchive",cascade="persist")
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
     * @return PostFlag
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
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return PostFlag
     */
    public function setPost(\P4M\CoreBundle\Entity\Post $post = null)
    {
        $this->post = $post;
    
        return $this;
    }

    /**
     * Get post
     *
     * @return \P4M\CoreBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set type
     *
     * @param \P4M\ModerationBundle\Entity\FlagType $type
     * @return PostFlag
     */
    public function setType(\P4M\ModerationBundle\Entity\FlagType $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \P4M\ModerationBundle\Entity\FlagType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return PostFlag
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
     * Constructor
     */
    public function __construct()
    {
        $this->confirmations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add confirmations
     *
     * @param \P4M\ModerationBundle\Entity\FlagConfirmation $confirmations
     * @return PostFlagArchive
     */
    public function addConfirmation(\P4M\ModerationBundle\Entity\FlagConfirmation $confirmations)
    {
        $this->confirmations[] = $confirmations;
    
        return $this;
    }

    /**
     * Remove confirmations
     *
     * @param \P4M\ModerationBundle\Entity\FlagConfirmation $confirmations
     */
    public function removeConfirmation(\P4M\ModerationBundle\Entity\FlagConfirmation $confirmations)
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
