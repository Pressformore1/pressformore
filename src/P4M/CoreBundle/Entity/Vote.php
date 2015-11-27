<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\VoteRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Vote
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
     * @var integer
     *
     * @ORM\Column(name="Score", type="integer")
     */
    private $score;
    
     /**
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User",inversedBy="votes",cascade="persist")
     */
    private $user;
    
    /**
     *@ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post",inversedBy="votes",cascade="persist")
     */
    private $post;
    /**
     *@ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Comment",inversedBy="votes",cascade="persist")
     */
    private $comment;
    /**
     *@ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Wall",inversedBy="votes",cascade="persist")
     */
    private $wall;
    
    /**
     *
     * @ORM\Column(name="date",type="datetime")
     */
    private $date;
    
     /**
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\UserActivity",mappedBy="vote",cascade="remove")
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
     * Set score
     *
     * @param integer $score
     * @return Vote7
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return Vote
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
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return Vote
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
     * Set comment
     *
     * @param \P4M\CoreBundle\Entity\Comment $comment
     * @return Vote
     */
    public function setComment(\P4M\CoreBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return \P4M\CoreBundle\Entity\Comment 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set wall
     *
     * @param \P4M\CoreBundle\Entity\Wall $wall
     * @return Vote
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
     * Set date
     *
     * @param \dateTime $date
     * @return Vote
     */
    public function setDate(\dateTime $date)
    {
        $this->date = $date;
    
        return $this;
    }
    
    /**
    * @ORM\PrePersist
    */
    private function forceDate()
    {
        $this->setDate(new \DateTime());
    }

    /**
     * Get date
     *
     * @return \dateTime 
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
     * @return Vote
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
}