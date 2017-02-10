<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Unpressform
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\UnpressformRepository")
 */
class Unpressform
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
     * @Groups({"info"})
     */
    private $date;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\UnpressformType")
     */    
    private $type;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post")
     */ 
    private $post;
    
    public function __construct()
    {
        $this->date=new \DateTime();
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
     * @return Unpressform
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
     * @return Unpressform
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
     * Set type
     *
     * @param \P4M\CoreBundle\Entity\UnpressformType $type
     * @return Unpressform
     */
    public function setType(\P4M\CoreBundle\Entity\UnpressformType $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \P4M\CoreBundle\Entity\UnpressformType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return Unpressform
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
}
