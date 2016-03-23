<?php

namespace P4M\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLink
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\UserBundle\Entity\UserLinkRepository")
 */
class UserLink
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
    * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="following",cascade="persist")
    * @ORM\JoinColumn(name="follower", referencedColumnName="id")
    */
    protected $follower;

    //Je me rend compte que mon anglais est merdique. Il faut lire following comme followed
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="followers",cascade="persist")
     * @ORM\JoinColumn(name="following", referencedColumnName="id")
     */
    protected $following;
    
    /**
     *
     * @var datetime
     * @ORM\Column(name="date",type="datetime") 
     */
    protected $date;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\UserActivity",mappedBy="userLink",cascade="remove")
     */
    private $activities;

    
    
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
     * Set follower
     *
     * @param \P4M\UserBundle\Entity\User $follower
     * @return UserLink
     */
    public function setFollower(\P4M\UserBundle\Entity\User $follower = null)
    {
        $this->follower = $follower;
    
        return $this;
    }

    /**
     * Get follower
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * Set following
     *
     * @param \P4M\UserBundle\Entity\User $following
     * @return UserLink
     */
    public function setFollowing(\P4M\UserBundle\Entity\User $following = null)
    {
        $this->following = $following;
    
        return $this;
    }

    /**
     * Get following
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserLink
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
    
    public function getDateAddedTimeStamp()
    {
        return $this->date->getTimestamp();
    }
    

    /**
     * Add activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     * @return UserLink
     */
    public function addActivitie(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities[] = $activities;
    
        return $this;
    }

    /**
     * Remove activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     */
    public function removeActivitie(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     * @return UserLink
     */
    public function addActivity(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     */
    public function removeActivity(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities->removeElement($activities);
    }
}
