<?php

namespace P4M\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\NotificationBundle\Entity\NotificationRepository")
 */
class Notification
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
     * @ORM\ManyToOne(targetEntity="P4M\NotificationBundle\Entity\NotificationType")
     */
    private $type;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="notifications")
     */
    private $user;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="viewed",type="boolean")
     */
    private $read;
    
    
    /**
     * @var datetime
     * @ORM\Column(name="date",type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\TrackingBundle\Entity\UserActivity",inversedBy="notifications")
     */
    private $activity;
    
    
    
    
    
    public function __construct()
    {
        
        $this->read = false;
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
     * Set type
     *
     * @param string $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Notification
     */
    public function setUser(\P4M\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        $user->addNotification($this);
    
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
     * Set read
     *
     * @param boolean $read
     * @return Notification
     */
    public function setRead($read)
    {
        $this->read = $read;
    
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

   

    /**
     * Set activity
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activity
     * @return Notification
     */
    public function setActivity(\P4M\TrackingBundle\Entity\UserActivity $activity = null)
    {
        $this->activity = $activity;
    
        return $this;
    }

    /**
     * Get activity
     *
     * @return \P4M\TrackingBundle\Entity\UserActivity 
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Notification
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
}
