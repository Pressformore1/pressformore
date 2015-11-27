<?php

namespace P4M\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserActivity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\TrackingBundle\Entity\UserActivityRepository")
 */
class UserActivity
{
    
    const TYPE_POST_ADDED = "post_added";
    const TYPE_POST_EDITED = "post_edited";
    const TYPE_POST_VOTED = "post_voted";
    const TYPE_POST_REPORTED = "post_reported";
    const TYPE_POST_COMMENTED = "post_commented";
    const TYPE_WALL_VOTED = "wall_voted";
    const TYPE_WALL_CREATED = "wall_created";
    const TYPE_WALL_EDITED = "wall_edited";
    const TYPE_WALL_COMMENTED = "wall_commented";
    const TYPE_COMMENT_VOTED = "comment_voted";
    const TYPE_USER_FOLLOWED = "user_followed";
    const TYPE_USER_UNFOLLOWED = "user_unfollowed";
    const TYPE_CUSTOMMER_WALLET_LOADED = "custommer_wallet_loaded";
    const TYPE_CUSTOMMER_WALLET_EXPIRED = "custommer_wallet_expired";
    const TYPE_PRODUCER_WALLET_LOADED = "producer_wallet_loaded";
    const TYPE_POST_PRESSFORMED = "post_pressformed";
    const TYPE_POST_AUTHOR_IDENTIFIED = "post_author_identified";
    const TYPE_POST_AUTHOR_CHANGED = "post_author_changed";
    
    private $type_allowed;
    
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Wall",inversedBy="activities")
     */
    private $wall;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Post",inversedBy="activities")
     */
    private $post;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Comment",inversedBy="activities")
     */
    private $comment;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Vote",inversedBy="activities")
     */
    private $vote;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\UserLink",inversedBy="activities")
     */
    private $userLink;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\NotificationBundle\Entity\Notification",mappedBy="activity",cascade="remove")
     */
    private $notifications;

    
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->type_allowed = array(
            self::TYPE_POST_ADDED,
            self::TYPE_POST_COMMENTED,
            self::TYPE_POST_EDITED,
            self::TYPE_POST_REPORTED,
            self::TYPE_POST_VOTED,
            self::TYPE_WALL_COMMENTED,
            self::TYPE_WALL_CREATED,
            self::TYPE_WALL_EDITED,
            self::TYPE_WALL_VOTED,
            self::TYPE_COMMENT_VOTED,
            self::TYPE_USER_FOLLOWED,
            self::TYPE_USER_UNFOLLOWED,
            self::TYPE_CUSTOMMER_WALLET_LOADED,
            self::TYPE_CUSTOMMER_WALLET_EXPIRED,
            self::TYPE_PRODUCER_WALLET_LOADED,
            self::TYPE_POST_PRESSFORMED,
            self::TYPE_POST_AUTHOR_IDENTIFIED,
            self::TYPE_POST_AUTHOR_CHANGED
                
                );
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
     * @return UserActivity
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
     * Set type
     *
     * @param string $type
     * @return UserActivity
     */
    public function setType($type)
    {
        if (in_array($type, $this->type_allowed))
        {
            $this->type = $type;
        }
        
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return UserActivity
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
     * @return UserActivity
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
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return UserActivity
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
     * @return UserActivity
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
     * Set vote
     *
     * @param \P4M\CoreBundle\Entity\Vote $vote
     * @return UserActivity
     */
    public function setVote(\P4M\CoreBundle\Entity\Vote $vote = null)
    {
        $this->vote = $vote;
    
        return $this;
    }

    /**
     * Get vote
     *
     * @return \P4M\CoreBundle\Entity\Vote 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Add notifications
     *
     * @param \P4M\NotificationBundle\Entity\Notification $notifications
     * @return UserActivity
     */
    public function addNotification(\P4M\NotificationBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
    
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \P4M\NotificationBundle\Entity\Notification $notifications
     */
    public function removeNotification(\P4M\NotificationBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
    
    public function getTypeAllowed()
    {
        return $this->type_allowed;
    }

    /**
     * Set userLink
     *
     * @param \P4M\UserBundle\Entity\UserLink $userLink
     * @return UserActivity
     */
    public function setUserLink(\P4M\UserBundle\Entity\UserLink $userLink = null)
    {
        $this->userLink = $userLink;
    
        return $this;
    }

    /**
     * Get userLink
     *
     * @return \P4M\UserBundle\Entity\UserLink 
     */
    public function getUserLink()
    {
        return $this->userLink;
    }
}