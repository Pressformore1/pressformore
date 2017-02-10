<?php

namespace P4M\ModerationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlagConfirmation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\ModerationBundle\Entity\FlagConfirmationRepository")
 */
class FlagConfirmation
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
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\PostFlag",inversedBy="confirmations")
     */
    private $flag;
    /**
     * @ORM\ManyToOne(targetEntity="P4M\ModerationBundle\Entity\PostFlagArchive",inversedBy="confirmations",cascade="persist")
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
     * @return FlagConfirmation
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
     * Set flag
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flag
     * @return FlagConfirmation
     */
    public function setFlag(\P4M\ModerationBundle\Entity\PostFlag $flag = null)
    {
        $this->flag = $flag;
    
        return $this;
    }

    /**
     * Get flag
     *
     * @return \P4M\ModerationBundle\Entity\PostFlag 
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return FlagConfirmation
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
     * Set flagArchive
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flagArchive
     * @return FlagConfirmation
     */
    public function setFlagArchive(\P4M\ModerationBundle\Entity\PostFlag $flagArchive = null)
    {
        $this->flagArchive = $flagArchive;
    
        return $this;
    }

    /**
     * Get flagArchive
     *
     * @return \P4M\ModerationBundle\Entity\PostFlag 
     */
    public function getFlagArchive()
    {
        return $this->flagArchive;
    }
}
