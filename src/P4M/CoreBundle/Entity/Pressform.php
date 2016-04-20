<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Pressform
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\PressformRepository")
 */
class Pressform
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
     * @ORM\Column(name="date", type="date")
     * @Groups("json")
     */
    private $date;

    
    /**
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User", inversedBy="sentPressforms")
     */
    private $sender;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post", inversedBy="pressforms")
     * @Groups("json")
     */
    private $post;
    
    
    /**
     *
     * @var boolean
     * @Groups("json")
     * @ORM\Column(name="payed",type="boolean")
     */
    private $payed;
    
    
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->payed = false;
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
     * @return Pressform
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
     * Set sender
     *
     * @param \P4M\UserBundle\Entity\User $sender
     * @return Pressform
     */
    public function setSender(\P4M\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;
    
        return $this;
    }

    /**
     * Get sender
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

   

    /**
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return Pressform
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
     * Set payed
     *
     * @param boolean $payed
     * @return Pressform
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;
    
        return $this;
    }

    /**
     * Get payed
     *
     * @return boolean 
     */
    public function getPayed()
    {
        return $this->payed;
    }
}