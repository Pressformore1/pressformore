<?php

namespace P4M\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * WPBlogPost
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\BlogBundle\Entity\WPBlogPostRepository")
 */
class WPBlogPost
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceUrl", type="string", length=255)
     */
    private $sourceUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var array
     *
     * @ORM\Column(name="anchors", type="array")
     */
    private $anchors;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
    * @Gedmo\Slug(fields={"title"})
    * @ORM\Column(length=128, unique=true)
    */
    private $slug;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User")
     */
    private $user;

    
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->anchors = array();
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
     * Set title
     *
     * @param string $title
     * @return WPBlogPost
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return WPBlogPost
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    
        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set sourceUrl
     *
     * @param string $sourceUrl
     * @return WPBlogPost
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    
        return $this;
    }

    /**
     * Get sourceUrl
     *
     * @return string 
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return WPBlogPost
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
     * Set anchors
     *
     * @param array $anchors
     * @return WPBlogPost
     */
    public function setAnchors($anchors)
    {
        $this->anchors = $anchors;
    
        return $this;
    }

    /**
     * Get anchors
     *
     * @return array 
     */
    public function getAnchors()
    {
        return $this->anchors;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return WPBlogPost
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
     * Set slug
     *
     * @param string $slug
     * @return WPBlogPost
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WPBlogPost
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
}
