<?php

namespace P4M\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostView
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\TrackingBundle\Entity\PostViewRepository")
 */
class PostView
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
     * @var \DateTime
     *
     * @ORM\Column(name="datein", type="datetime")
     */
    private $datein;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateout", type="datetime")
     */
    private $dateout;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User",inversedBy="postViews")
     */
    private $user;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Post",inversedBy="views")
     */
    private $post;

   
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->datein = new \DateTime();
        $this->dateout = new \DateTime();
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
     * Set datein
     *
     * @param \DateTime $datein
     * @return PostView
     */
    public function setDatein($datein)
    {
        $this->datein = $datein;
    
        return $this;
    }

    /**
     * Get datein
     *
     * @return \DateTime 
     */
    public function getDatein()
    {
        return $this->datein;
    }

    /**
     * Set dateout
     *
     * @param \DateTime $dateout
     * @return PostView
     */
    public function setDateout($dateout)
    {
        $this->dateout = $dateout;
    
        return $this;
    }

    /**
     * Get dateout
     *
     * @return \DateTime 
     */
    public function getDateout()
    {
        return $this->dateout;
    }
    
    
   

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    

    /**
     * Get post
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return PostView
     */
    public function setUser(\P4M\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return PostView
     */
    public function setPost(\P4M\CoreBundle\Entity\Post $post = null)
    {
        $this->post = $post;
    
        return $this;
    }
    
    public function getDateTimestamp()
    {
        return $this->datein->getTimestamp();
    }
}