<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;


/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"json"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     * @Groups({"json"})
     */
    private $name;

    /**
     *
     * @var type string
     * @ORM\Column(name="iconColor",type="string",length=255)
     * @Groups({"json"})
     */
    private $iconColor;
    
    /**
     *
     * @var type string
     * @ORM\Column(name="iconGrey",type="string",length=255)
     * @Groups({"json"})
     */
    private $iconGrey;
    
    
    
    /**
     *
     * @var type integer
     * 
     * @ORM\Column(name="wpId",type="integer",length=11,nullable=true)
     */
    private $wpId;
    
    /**
     *
     * @var text
     * 
     * @ORM\Column(name="description",type="text",nullable=true)
     * @Groups({"json"})
     */
    private $description;
    
    
    /**
     *@ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Post",cascade="persist",inversedBy="categories")
     */
    private $posts;
    
    /**
     *
     * @var boolean
     * @ORM\Column(name="draft",type="boolean")
     */
    private $draft;
    
    /**
     *
     * @var boolean
     * @ORM\Column(name="deleted",type="boolean")
     */
    private $deleted;
    
    
    
    private $newPostsCount;


    
    public function __construct()
    {
        $this->posts = array();
        $this->deleted = false;
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
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    
    /**
     * Add posts
     *
     * @param \P4M\CoreBundle\Entity\Post $posts
     * @return Category
     */
    public function addPost(\P4M\CoreBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \P4M\CoreBundle\Entity\Post $posts
     */
    public function removePost(\P4M\CoreBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set wpId
     *
     * @param integer $wpId
     * @return Category
     */
    public function setWpId($wpId)
    {
        $this->wpId = $wpId;
    
        return $this;
    }

    /**
     * Get wpId
     *
     * @return integer 
     */
    public function getWpId()
    {
        return $this->wpId;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Category
     */
    public function setColor($color)
    {
        $this->color = $color;
    
        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
 
    
    /*
     * returns int
     */
    public function getNewPostsCount()
    {
        return $this->newPostsCount;
    }
    
    public function setNewPostsCount($newPostCount)
    {
        $this->newPostsCount = $newPostCount;
        
        return $this;
        
    }
    
   
    
    
    public function __toString() 
    {
        return $this->name;        
    }   
    
    

    /**
     * Set draft
     *
     * @param boolean $draft
     * @return Category
     */
    public function setDraft($draft)
    {
        $this->draft = $draft;
    
        return $this;
    }

    /**
     * Get draft
     *
     * @return boolean 
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Category
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

   /**
     * Set iconColor
     *
     * @param string $iconColor
     * @return Category
     */
    public function setIconColor($iconColor)
    {
        $this->iconColor = $iconColor;
    
        return $this;
    }

    /**
     * Get iconColor
     *
     * @return string 
     */
    public function getIconColor()
    {
        return $this->iconColor;
    }

    /**
     * Set iconGrey
     *
     * @param string $iconGrey
     * @return Category
     */
    public function setIconGrey($iconGrey)
    {
        $this->iconGrey = $iconGrey;
    
        return $this;
    }

    /**
     * Get iconGrey
     *
     * @return string 
     */
    public function getIconGrey()
    {
        return $this->iconGrey;
    }
    public function toArray(){

    }
}