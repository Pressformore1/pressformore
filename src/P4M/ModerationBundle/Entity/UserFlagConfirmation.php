<?php

namespace P4M\ModerationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WallFlagConfirmation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\ModerationBundle\Entity\WallFlagConfirmationRepository")
 */
class UserFlagConfirmation
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
     * @var boolean
     *
     * @ORM\Column(name="confirmed", type="boolean")
     */
    private $confirmed;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User",inversedBy="confirmations")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\UserFlag",inversedBy="confirmations")
     */
    private $flag;
    /**
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\UserFlagArchive",inversedBy="confirmations",cascade="persist")
     */
    private $flagArchive;


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
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return WallFlagConfirmation
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    
        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WallFlagConfirmation
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
     * Set flag
     *
     * @param \P4M\ModerationBundle\Entity\WallFlag $flag
     * @return WallFlagConfirmation
     */
    public function setFlag(\P4M\ModerationBundle\Entity\UserFlag $flag = null)
    {
        $this->flag = $flag;
    
        return $this;
    }

    /**
     * Get flag
     *
     * @return \P4M\ModerationBundle\Entity\WallFlag 
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set flagArchive
     *
     * @param \P4M\ModerationBundle\Entity\WallFlagArchive $flagArchive
     * @return WallFlagConfirmation
     */
    public function setFlagArchive(\P4M\ModerationBundle\Entity\UserFlagArchive $flagArchive = null)
    {
        $this->flagArchive = $flagArchive;
    
        return $this;
    }

    /**
     * Get flagArchive
     *
     * @return \P4M\ModerationBundle\Entity\WallFlagArchive 
     */
    public function getFlagArchive()
    {
        return $this->flagArchive;
    }
}
