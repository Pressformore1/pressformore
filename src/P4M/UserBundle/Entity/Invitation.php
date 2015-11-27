<?php

namespace P4M\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\UserBundle\Entity\InvitationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Invitation
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
     * @ORM\Column(name="code", type="string", length=10,unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,unique=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sent", type="boolean")
     */
    private $sent;
    
    /** @ORM\ManyToOne(targetEntity="User", cascade={"persist", "merge"}) */
    protected $user;
    
    /**
     *
     * @ORM\Column(name="date",type="datetime",nullable=true)
     */
    private $date;
    
    /**
     *
     * @ORM\Column(name="maxIterations",type="integer")
     */
    private $maxIterations;
    
    /**
     *
     * @ORM\Column(name="iterations",type="integer")
     */
    private $iterations;


    
    public function __construct()
    {
        $this->sent = false;
        $this->date = new \DateTime();
        $this->maxIterations = 0;
        $this->iterations = 0;
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
     * Set code
     *
     * @param string $code
     * @return Invitation
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function generateCode()
    {
        if ($this->email)
        {
            $this->setCode(hash('sha512',  uniqid().$this->email));
        }
        
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
     * Set email
     *
     * @param string $email
     * @return Invitation
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     * @return Invitation
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    
        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean 
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return Invitation
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
     * Set date
     *
     * @param \DateTime $date
     * @return Invitation
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set maxIterations
     *
     * @param integer $maxIterations
     * @return Invitation
     */
    public function setMaxIterations($maxIterations)
    {
        $this->maxIterations = $maxIterations;
    
        return $this;
    }

    /**
     * Get maxIterations
     *
     * @return integer 
     */
    public function getMaxIterations()
    {
        return $this->maxIterations;
    }

    /**
     * Set iterations
     *
     * @param integer $iterations
     * @return Invitation
     */
    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
    
        return $this;
    }

    /**
     * Get iterations
     *
     * @return integer 
     */
    public function getIterations()
    {
        return $this->iterations;
    }
}