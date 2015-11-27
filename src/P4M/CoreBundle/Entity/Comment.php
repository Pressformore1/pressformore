<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
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
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdated", type="datetime")
     */
    private $dateUpdated;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text",length=65535)
     */
    private $content;
    
    /**
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User",inversedBy="comments",cascade="persist")
     */
    private $user;
    
    /**
     *@ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post",inversedBy="comments",cascade="persist")
     */
    private $post;
    
    /**
     *@ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Wall",inversedBy="comments",cascade="persist")
     */
    private $wall;
    
    /**
     * @ORM\OneToMany(targetEntity="\P4M\CoreBundle\Entity\Comment", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Comment", inversedBy="children")
     */
    private $parent;
    
    /**
     *@ORM\OneToMany(targetEntity="\P4M\CoreBundle\Entity\Vote",mappedBy="comment",cascade="remove")
     */
    private $votes;
    
    /**
     * @ORM\OneToMany(targetEntity="\P4M\TrackingBundle\Entity\UserActivity",mappedBy="comment",cascade="remove")
     */
    private $activities;

    
    public function __construct()
    {
        $this->setDateAdded(new \DateTime());
        $this->setDateUpdated(new \DateTime());
        $this->votes =  array();
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Comment
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    
        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return Comment
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }
    
     /**
    * @ORM\PrePersist
    */
    private function forceDateUpdate()
    {
        $this->setDateUpdated(new \DateTime());
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return Comment
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
     * @return Comment
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
     * Add children
     *
     * @param \P4M\CoreBundle\Entity\Comment $children
     * @return Comment
     */
    public function addChildren(\P4M\CoreBundle\Entity\Comment $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \P4M\CoreBundle\Entity\Comment $children
     */
    public function removeChildren(\P4M\CoreBundle\Entity\Comment $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \P4M\CoreBundle\Entity\Comment $parent
     * @return Comment
     */
    public function setParent(\P4M\CoreBundle\Entity\Comment $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \P4M\CoreBundle\Entity\Comment 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add votes
     *
     * @param \P4M\CoreBundle\Entity\Vote $votes
     * @return Comment
     */
    public function addVote(\P4M\CoreBundle\Entity\Vote $votes)
    {
        $this->votes[] = $votes;
    
        return $this;
    }

    /**
     * Remove votes
     *
     * @param \P4M\CoreBundle\Entity\Vote $votes
     */
    public function removeVote(\P4M\CoreBundle\Entity\Vote $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVotes()
    {
        return $this->votes;
    }
    
    public function getPositiveVoteNumber()
    {
        $count = 0;
        foreach ($this->votes as $vote)
        {
            if ($vote->getScore()>0)
            {
                $count ++;
            }
            
        }
        
        return $count;
    }
    
    public function getNegativeVoteNumber()
    {
        $count = 0;
        foreach ($this->votes as $vote)
        {
            if ($vote->getScore()<0)
            {
                $count ++;
            }
            
        }
        
        return $count;
    }

    /**
     * Set wall
     *
     * @param \P4M\CoreBundle\Entity\Wall $wall
     * @return Comment
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
    
    public function getDateAddedTimeStamp()
    {
        return $this->dateAdded->getTimestamp();
    }
}