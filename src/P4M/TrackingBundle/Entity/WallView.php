<?php

namespace P4M\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WallView
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\TrackingBundle\Entity\WallViewRepository")
 */
class WallView
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="wallViews")
     */
    private $user;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Wall", inversedBy="views")
     */
    private $wall;

    
    
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     * @return WallView
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
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WallView
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
     * Set wall
     *
     * @param \P4M\CoreBundle\Entity\Wall $wall
     * @return WallView
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
}
