<?php

namespace P4M\TrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostArchive
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\TrackingBundle\Entity\PostArchiveRepository")
 */
class PostArchive
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceUrl", type="string", length=255)
     */
    private $sourceUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128,unique=false)
     */
    private $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEdited", type="datetime")
     */
    private $dateEdited;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\PostType")
     * @ORM\JoinColumn(nullable=false)
     */
     private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userEdit;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Category")
     * 
     */
    private $categories;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")

     * 
     */
    private $tags;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Lang")
     */
    private $lang;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Post", inversedBy="archives")
     */
    private $originalPost;
    


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
     * @return PostArchive
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
     * Set content
     *
     * @param string $content
     * @return PostArchive
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
     * Set sourceUrl
     *
     * @param string $sourceUrl
     * @return PostArchive
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
     * Set status
     *
     * @param string $status
     * @return PostArchive
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return PostArchive
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
     * Set dateEdited
     *
     * @param \DateTime $dateEdited
     * @return PostArchive
     */
    public function setDateEdited($dateEdited)
    {
        $this->dateEdited = $dateEdited;
    
        return $this;
    }

    /**
     * Get dateEdited
     *
     * @return \DateTime 
     */
    public function getDateEdited()
    {
        return $this->dateEdited;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set type
     *
     * @param \P4M\CoreBundle\Entity\PostType $type
     * @return PostArchive
     */
    public function setType(\P4M\CoreBundle\Entity\PostType $type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \P4M\CoreBundle\Entity\PostType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set userEdit
     *
     * @param \P4M\UserBundle\Entity\User $userEdit
     * @return PostArchive
     */
    public function setUserEdit(\P4M\UserBundle\Entity\User $userEdit)
    {
        $this->userEdit = $userEdit;
    
        return $this;
    }

    /**
     * Get userEdit
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getUserEdit()
    {
        return $this->userEdit;
    }

    /**
     * Add categories
     *
     * @param \P4M\CoreBundle\Entity\Category $categories
     * @return PostArchive
     */
    public function addCategorie(\P4M\CoreBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \P4M\CoreBundle\Entity\Category $categories
     */
    public function removeCategorie(\P4M\CoreBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Add tags
     *
     * @param \P4M\CoreBundle\Entity\Tag $tags
     * @return PostArchive
     */
    public function addTag(\P4M\CoreBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    
        return $this;
    }

    /**
     * Remove tags
     *
     * @param \P4M\CoreBundle\Entity\Tag $tags
     */
    public function removeTag(\P4M\CoreBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Set lang
     *
     * @param \P4M\CoreBundle\Entity\Lang $lang
     * @return PostArchive
     */
    public function setLang(\P4M\CoreBundle\Entity\Lang $lang = null)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \P4M\CoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }

    

    

    /**
     * Set originalPost
     *
     * @param \P4M\CoreBundle\Entity\Post $originalPost
     * @return PostArchive
     */
    public function setOriginalPost(\P4M\CoreBundle\Entity\Post $originalPost = null)
    {
        $this->originalPost = $originalPost;
    
        return $this;
    }

    /**
     * Get originalPost
     *
     * @return \P4M\CoreBundle\Entity\Post 
     */
    public function getOriginalPost()
    {
        return $this->originalPost;
    }
}