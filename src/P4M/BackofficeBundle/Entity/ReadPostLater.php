<?php

namespace P4M\BackofficeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * ReadPostLater
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\BackofficeBundle\Entity\ReadPostLaterRepository")
 */
class ReadPostLater
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
     * @Groups({"list"})
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User",inversedBy="readLater")
     */
    private $user;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post",inversedBy="readLater",cascade="persist")
     */
    private $post;
    
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
     * @return ReadPostLater
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
     * @return ReadPostLater
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
     * @return ReadPostLater
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
    
    public function getDateTimestamp()
    {
        return $this->date->getTimestamp();
    }
}
