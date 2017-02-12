<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{

    const STATUS_PUBLISHED = "status_published";
    const STATUS_DRAFT = "status_draft";
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"post","list", "json", "donator"})
     */
    private $id;
    
    /**
     *
     * @Assert\Length(min = 20)
     * @var string
     *
     * @ORM\Column(name="title",type="string",length=255)
     * @Groups({"post","info", "list", "json", "donator"})
     */
    
    private $title;
    
    
    /**
     * @var string
     * @ORM\Column(name="picture", type="string", length=255)
     * @Groups({"post","list", "donator"})
     */
    private $picture;
    
    /**
     * @var string
     *
     * @ORM\Column(name="localPicture", type="string", length=255,nullable=true)
     */
    private $localPicture;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Groups({"post","list"})
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="sourceUrl", type="string", length=255,unique = true)
     * @Groups({"post","info","list", "donator", "json"})
     */
    private $sourceUrl;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateAdded", type="datetime")
     * @Groups({"post"})
     */
    private $dateAdded;

    
    /**
     * @Groups({"post"})
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\PostType",cascade="persist")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User",inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @Groups({"post"})
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Category",cascade="persist",mappedBy="posts")
     */
    private $categories;
    
    /**
     * @Groups({"post"})
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Tag",cascade="persist",mappedBy="posts")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Comment",cascade={"persist","remove"},mappedBy="post")
     */
//    @ORM\OrderBy({"dateAdded" = "DESC"})
    private $comments;
    
    /**
     *
     * @var string
     * 
     * @ORM\Column(name="status",type="string",length=20)
     */
    private $status;
    
    /**
     *
     * @var integer
     * 
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\PostView",cascade="remove",mappedBy="post")
     */
    private $views;
    
    /**
    * @Gedmo\Slug(fields={"title"})
    * @ORM\Column(length=128, unique=true)
    * @Groups({"post","info", "json", "donator"})
    */
    private $slug;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\CoreBundle\Entity\Lang",cascade="persist")
     */
    private $lang;
    
    /**
     *
     * @var array
     * @ORM\Column(name="pictureList",type="array")
     */
    private $pictureList;
    
    /**
     * @ORM\OneToMany(targetEntity="\P4M\CoreBundle\Entity\Vote",mappedBy="post",cascade="remove")
     * 
     */
//    @ORM\OrderBy({"date" = "DESC"})
    private $votes;
    
    /**
     * @ORM\OneToOne(targetEntity="\P4M\ModerationBundle\Entity\PostFlag",mappedBy="post", cascade="remove")
     */
    private $flag;
    
    /**
     * @ORM\Column(name="confirmedFlag",type="boolean",nullable=true)
     */
    private $confirmedFlag;
    
    /**
     * @Groups({"list"})
     * @ORM\OneToMany(targetEntity="\P4M\BackofficeBundle\Entity\ReadPostLater",mappedBy="post",cascade="remove") 
     */
    private $readLater;
    
    
    /**
     *
     * @ORM\OneToOne(targetEntity="\P4M\PinocchioBundle\Entity\PostScore",mappedBy="post",cascade="remove") 
     */
    private $score;
    
    
    /**
     *
     * @ORM\OneToMany(targetEntity="\P4M\TrackingBundle\Entity\PostArchive", mappedBy="originalPost",cascade="remove")
     */
    private $archives;
    
    /**
     *
     * @var boolean
     * @ORM\Column(name="showOnHome",type="boolean")
     */
    private $showOnHome;
    
    /**
     *
     * @var boolean
     * @ORM\Column(name="blogPost",type="boolean")
     */
    private $blogPost;
    /**
     *
     * @var array
     * @ORM\Column(name="anchors",type="array")
     */
    private $anchors;
    
    /**
     *
     * @var boolean
     * @ORM\Column(name="quarantaine",type="boolean")
     */
    private $quarantaine;
    
    
    
     /**
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\UserActivity",mappedBy="post",cascade="remove")
     */
    private $activities;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\BackofficeBundle\Entity\BannedPost",mappedBy="post",cascade="remove")
     */
    private $userBanned;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User",inversedBy="productions")
     * @Groups({"post","list", "json", "donator"})
     */
    private $author;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Pressform",mappedBy="post")
     * @Groups({"info","donator"})
     */
    private $pressforms;

    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Unpressform",mappedBy="post")
     * @Groups({"info"})
     */
    private $unpressforms;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\WantPressform",mappedBy="post")
     * @Groups({"donator"})
     */
    private $wantPressforms;
    
    /**
     * @ORM\Column(name="lastScanned",type="date")
     */
    private $lastScanned;

    /**
     * @ORM\Column(name="embed",type="text",nullable=true)
     */
    private $embed;
    
    
    /**
     * @ORM\Column(name="iframe_allowed",type="boolean")
     * 
     */
    private $iframeAllowed = true;
    
    /**
     *
     * @Groups({"post"})
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\TempAuthor", mappedBy="post", cascade="remove")
     */
    private $tempAuthor;
    
    public function __toString() {
        return $this->title;
    }
    
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->views = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->readLater = new ArrayCollection();
        $this->pictureList = array();
        $this->dateAdded = new \DateTime();
        $this->lastScanned = new \DateTime();
        $this->status = 0;
        $this->quarantaine = false;
        $this->confirmedFlag = false;
        $this->showOnHome = false;
        $this->blogPost = false;
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
     * Set picture
     *
     * @param string $picture
     * @return Post
     */
    public function setPicture($picture)
    {
        
        $this->picture = (string) $picture;
    
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
     * Set content
     *
     * @param string $content
     * @return Post
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
     * @return Post
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Post
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
     * Set type
     *
     * @param \P4M\CoreBundle\Entity\PostType $type
     * @return Post
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
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\P4M\UserBundle\Entity\User $user)
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
     * Set title
     *
     * @param string $title
     * @return Post
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
     * Add categories
     *
     * @param \P4M\CoreBundle\Entity\Category $categories
     * @return Post
     */
    public function addCategorie(\P4M\CoreBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;
        $categories->addPost($this);
    
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
    public function setCategories(\Doctrine\Common\Collections\Collection $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Post
     */
    public function setStatus($status)
    {
        if ($status == self::STATUS_DRAFT || $status == self::STATUS_PUBLISHED)
        {
            $this->status = $status;
            return $this;
        }
        else
        {
            return false;
        }
        
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
     * @return Post
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
     * Add tags
     *
     * @param \P4M\CoreBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\P4M\CoreBundle\Entity\Tag $tags)
    {
        if (!$this->tags->contains($tags))
        {
            $this->tags->add($tags);
        }
    
    
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
    /**
     * Set tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function setTags(\Doctrine\Common\Collections\Collection $tags )
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Add comments
     *
     * @param \P4M\CoreBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment(\P4M\CoreBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \P4M\CoreBundle\Entity\Comment $comments
     */
    public function removeComment(\P4M\CoreBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set lang
     *
     * @param \P4M\CoreBundle\Entity\Lang $lang
     * @return Post
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
     * Set pictureList
     *
     * @param array $pictureList
     * @return Post
     */
    public function setPictureList($pictureList)
    {
        $this->pictureList = $pictureList;
    
        return $this;
    }

    /**
     * Get pictureList
     *
     * @return array 
     */
    public function getPictureList()
    {
        return $this->pictureList;
    }

    /**
     * Add votes
     *
     * @param \P4M\CoreBundle\Entity\Vote $votes
     * @return Post
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

    /**
     * Add views
     *
     * @param \P4M\CoreBundle\Entity\Comment $views
     * @return Post
     */
    public function addView(\P4M\CoreBundle\Entity\Comment $views)
    {
        $this->views[] = $views;
    
        return $this;
    }

    /**
     * Remove views
     *
     * @param \P4M\CoreBundle\Entity\Comment $views
     */
    public function removeView(\P4M\CoreBundle\Entity\Comment $views)
    {
        $this->views->removeElement($views);
    }

    /**
     * Get views
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViews()
    {
        return $this->views;
    }
    
    
   /**
     * Add flags
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flags
     * @return Post
     */
    public function addFlag(\P4M\ModerationBundle\Entity\PostFlag $flags)
    {
        $this->flags[] = $flags;
    
        return $this;
    }

    /**
     * Remove flags
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flags
     */
    public function removeFlag(\P4M\ModerationBundle\Entity\PostFlag $flags)
    {
        $this->flags->removeElement($flags);
    }

    /**
     * Get flags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFlags()
    {
        return $this->flags;
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
     * Add readLater
     *
     * @param \P4M\BackofficeBundle\Entity\ReadPostLater $readLater
     * @return Post
     */
    public function addReadLater(\P4M\BackofficeBundle\Entity\ReadPostLater $readLater)
    {
        $this->readLater[] = $readLater;
    
        return $this;
    }

    /**
     * Remove readLater
     *
     * @param \P4M\BackofficeBundle\Entity\ReadPostLater $readLater
     */
    public function removeReadLater(\P4M\BackofficeBundle\Entity\ReadPostLater $readLater)
    {
        $this->readLater->removeElement($readLater);
    }

    /**
     * Get readLater
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReadLater()
    {
        return $this->readLater;
    }

    /**
     * Set flag
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flag
     * @return Post
     */
    public function setFlag(\P4M\ModerationBundle\Entity\PostFlag $flag = null)
    {
        $this->flag = $flag;
    
        return $this;
    }

    /**
     * Get flag
     *
     * @return \P4M\ModerationBundle\Entity\PostFlag 
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Set confirmedFlag
     *
     * @param boolean $confirmedFlag
     * @return Post
     */
    public function setConfirmedFlag($confirmedFlag)
    {
        $this->confirmedFlag = $confirmedFlag;
    
        return $this;
    }

    /**
     * Get confirmedFlag
     *
     * @return boolean 
     */
    public function getConfirmedFlag()
    {
        return $this->confirmedFlag;
    }
    
    
    public function isSearchable()
    {
        if ($this->confirmedFlag)
        {
            return false;
        }
        return true;
    }

    /**
     * Set score
     *
     * @param \P4M\PinocchioBundle\Entity\PostScore $score
     * @return Post
     */
    public function setScore(\P4M\PinocchioBundle\Entity\PostScore $score = null)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return \P4M\PinocchioBundle\Entity\PostScore 
     */
    public function getScore()
    {
        return $this->score;
    }
    
    public function getCommentsCount()
    {
        if(!$this->comments)
            return ;
        return $this->comments->count();
    }
    
    public function getViewsCount()
    {
        return $this->views->count();
    }
    
    public function getDateAddedTimeStamp()
    {
        return $this->dateAdded->getTimestamp();
    }

    /**
     * Set showOnHome
     *
     * @param boolean $showOnHome
     * @return Post
     */
    public function setShowOnHome($showOnHome)
    {
        $this->showOnHome = $showOnHome;
    
        return $this;
    }

    /**
     * Get showOnHome
     *
     * @return boolean 
     */
    public function getShowOnHome()
    {
        return $this->showOnHome;
    }

    /**
     * Add archives
     *
     * @param \P4M\TrackingBundle\Entity\PostArchive $archives
     * @return Post
     */
    public function addArchive(\P4M\TrackingBundle\Entity\PostArchive $archives)
    {
        $this->archives[] = $archives;
    
        return $this;
    }

    /**
     * Remove archives
     *
     * @param \P4M\TrackingBundle\Entity\PostArchive $archives
     */
    public function removeArchive(\P4M\TrackingBundle\Entity\PostArchive $archives)
    {
        $this->archives->removeElement($archives);
    }

    /**
     * Get archives
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * Set blogPost
     *
     * @param boolean $blogPost
     * @return Post
     */
    public function setBlogPost($blogPost)
    {
        $this->blogPost = $blogPost;
    
        return $this;
    }

    /**
     * Get blogPost
     *
     * @return boolean 
     */
    public function getBlogPost()
    {
        return $this->blogPost;
    }

    /**
     * Set anchors
     *
     * @param array $anchors
     * @return Post
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
     * Set localPicture
     *
     * @param string $localPicture
     * @return Post
     */
    public function setLocalPicture($localPicture)
    {
        $this->localPicture = $localPicture;
    
        return $this;
    }

    /**
     * Get localPicture
     *
     * @return string 
     */
    public function getLocalPicture()
    {
        return $this->localPicture;
    }
    
    
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    

    public function grabFile()
    {

        $handle = curl_init($this->getPicture());
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
//        die('code'.$httpCode);
        if($httpCode == 200) 
        {

            $type = curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
            if ($type = 'image/jpeg')
            {
                $extension = '.jpg';
            }
            else if ($type = "image/png")
            {
                $exension = '.png';
            }
            else if ($type="image/gif")
            {
                $extension = '.gif';
            }
            
            
            
            if (isset($extension))
            {
//                die(utf8_encode($this->stripAccents($this->getTitle())));
                $path = __DIR__.'/../../../../web/images/posts/'.substr($this->slugify($this->getTitle()),0,10).$this->dateAdded->getTimestamp().$extension;
                
//                $fp = fopen ($path, 'w+');
                if(file_exists($path)){
//                    die('exists');
                    unlink($path);
                }
               
                $fp = fopen($path,'x');
                fwrite($fp, $response);
                fclose($fp);
//             
                
                $this->setLocalPicture('images/posts/'.substr($this->slugify($this->getTitle()),0,10).$this->dateAdded->getTimestamp().$extension);
                
//                die($path);
                chmod($path, 0644);
            }
//            else
//            {
//                die('not 200 '.$httpCode);
//            }
            
//            
//            
//
//            if (copy($this->getPicture(),$path))
//            {
//                $this->setLocalPicture('images/posts/'.substr(utf8_encode($this->stripAccents($this->getTitle())),0,10).$this->dateAdded->getTimestamp().$extension);
//                chmod($path, 0644);
//            }
        }
            
        curl_close($handle);
        
    }
    
     private function slugify($str, $char = '' )
    {

       // Lower case the string and remove whitespace from the beginning or end
       $str = trim(strtolower($str));

       // Remove single quotes from the string
//       $str = str_replace(“‘”, ”, $str);

       // Every character other than a-z, 0-9 will be replaced with a single dash (-)
       $str = preg_replace("/[^a-z0-9]+/", $char, $str);

       // Remove any beginning or trailing dashes
       $str = trim($str, $char);
//       die('slugify'.$str);
        return $str;
     }
    
    private function normalize ($string) {
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',' '=>'-'
    );
    
    return strtr($string, $table);
}
    
    
    private function stripAccents($string){
            $string = strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
    'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
            $string = str_replace(' ','',$string);
            return str_replace(':','',$string);
    }

    /**
     * Set quarantaine
     *
     * @param boolean $quarantaine
     * @return Post
     */
    public function setQuarantaine($quarantaine)
    {
        $this->quarantaine = $quarantaine;
    
        return $this;
    }

    /**
     * Get quarantaine
     *
     * @return boolean 
     */
    public function getQuarantaine()
    {
        return $this->quarantaine;
    }

    /**
     * Add activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     * @return Post
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

    /**
     * Add userBanned
     *
     * @param \P4M\BackofficeBundle\Entity\BannedPost $userBanned
     * @return Post
     */
    public function addUserBanned(\P4M\BackofficeBundle\Entity\BannedPost $userBanned)
    {
        $this->userBanned[] = $userBanned;
    
        return $this;
    }

    /**
     * Remove userBanned
     *
     * @param \P4M\BackofficeBundle\Entity\BannedPost $userBanned
     */
    public function removeUserBanned(\P4M\BackofficeBundle\Entity\BannedPost $userBanned)
    {
        $this->userBanned->removeElement($userBanned);
    }

    /**
     * Get userBanned
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserBanned()
    {
        return $this->userBanned;
    }

    /**
     * Set author
     *
     * @param \P4M\UserBundle\Entity\User $author
     * @return Post
     */
    public function setAuthor(\P4M\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \P4M\UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add pressforms
     *
     * @param \P4M\CoreBundle\Entity\Pressform $pressforms
     * @return Post
     */
    public function addPressform(\P4M\CoreBundle\Entity\Pressform $pressforms)
    {
        $this->pressforms[] = $pressforms;
    
        return $this;
    }

    /**
     * Remove pressforms
     *
     * @param \P4M\CoreBundle\Entity\Pressform $pressforms
     */
    public function removePressform(\P4M\CoreBundle\Entity\Pressform $pressforms)
    {
        $this->pressforms->removeElement($pressforms);
    }

    /**
     * Get pressforms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPressforms()
    {
        return $this->pressforms;
    }

    /**
     * Add pressforms
     *
     * @param \P4M\CoreBundle\Entity\Pressform $pressforms
     * @return Post
     */
    public function addWantPressform(\P4M\CoreBundle\Entity\WantPressform $wantPressforms)
    {
        $this->wantPressforms[] = $wantPressforms;
    
        return $this;
    }

    /**
     * Remove pressforms
     *
     * @param \P4M\CoreBundle\Entity\Pressform $pressforms
     */
    public function removeWantPressform(\P4M\CoreBundle\Entity\WantPressform $wantPressforms)
    {
        $this->wantPressforms->removeElement($wantPressforms);
    }

    /**
     * Get pressforms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWantPressforms()
    {
        return $this->wantPressforms;
    }

    /**
     * Set lastScanned
     *
     * @param \DateTime $lastScanned
     * @return Post
     */
    public function setLastScanned($lastScanned)
    {
        $this->lastScanned = $lastScanned;
    
        return $this;
    }

    /**
     * Get lastScanned
     *
     * @return \DateTime 
     */
    public function getLastScanned()
    {
        return $this->lastScanned;
    }

    /**
     * Set embed
     *
     * @param string $embed
     * @return Post
     */
    public function setEmbed($embed)
    {
        $this->embed = $embed;
    
        return $this;
}

    /**
     * Get embed
     *
     * @return string 
     */
    public function getEmbed()
    {
        return $this->embed;
    }
    
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    private function securizeEmbed()
    {
        if (!preg_match('#^<iframe[^<]+</iframe>$#', $this->embed))
        {
            $this->embed= null;
            return null;
}
        $pfmAttributes = '';
        if (!preg_match('#id="post_iframe"#', $this->embed))
        {
            $pfmAttributes = ' id="post_iframe"';
        }
//        if (!preg_match('#src="//player.vimeo.com#', $this->embed) && !preg_match('#sandbox="allow-scripts"#', $this->embed)) // Un jour Vimeo acceptera surement les iframes sandboxées
//        {
//            $pfmAttributes.=  'sandbox="allow-scripts" ';
//        }
        
        $embed = substr($this->embed,0,  stripos($this->embed, ' ')).$pfmAttributes.substr($this->embed, stripos($this->embed, ' '));
        
        $this->embed = $embed;
        
        
        
    }

    /**
     * Set iframeAllowed
     *
     * @param boolean $iframeAllowed
     * @return Post
     */
    public function setIframeAllowed($iframeAllowed)
    {
        $this->iframeAllowed = $iframeAllowed;
    
        return $this;
    }

    /**
     * Get iframeAllowed
     *
     * @return boolean 
     */
    public function getIframeAllowed()
    {
        return $this->iframeAllowed;
    }

    /**
     * Set tempAuthor
     *
     * @param \P4M\CoreBundle\Entity\TempAuthor $tempAuthor
     * @return Post
     */
    public function setTempAuthor(\P4M\CoreBundle\Entity\TempAuthor $tempAuthor = null)
    {
        $this->tempAuthor = $tempAuthor;
    
        return $this;
}

    /**
     * Get tempAuthor
     *
     * @return \P4M\CoreBundle\Entity\TempAuthor 
     */
    public function getTempAuthor()
    {
        return $this->tempAuthor;
    }


    public function toArray(){
        $data['slug'] = $this->getSlug();
        $data['title'] = $this->getTitle();
        $data['content'] = $this->getContent();
        $data['lang'] = $this->getLang()->getCode();
        $data['picture'] = $this->getPicture();
        $data['SourceUrl'] = $this->getSourceUrl();
        $pictureList = $this->getPictureList();
        foreach($pictureList as $key => $value){
            $data['pictureList'][$key] = $value;
        }
        return $data;
    }


    /**
     * Add categories
     *
     * @param \P4M\CoreBundle\Entity\Category $categories
     * @return Post
     */
    public function addCategory(\P4M\CoreBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \P4M\CoreBundle\Entity\Category $categories
     */
    public function removeCategory(\P4M\CoreBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Add activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     * @return Post
     */
    public function addActivity(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \P4M\TrackingBundle\Entity\UserActivity $activities
     */
    public function removeActivity(\P4M\TrackingBundle\Entity\UserActivity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Add unpressforms
     *
     * @param \P4M\CoreBundle\Entity\UnPressform $unpressforms
     * @return Post
     */
    public function addUnpressform(\P4M\CoreBundle\Entity\UnPressform $unpressforms)
    {
        $this->unpressforms[] = $unpressforms;

        return $this;
    }

    /**
     * Remove unpressforms
     *
     * @param \P4M\CoreBundle\Entity\UnPressform $unpressforms
     */
    public function removeUnpressform(\P4M\CoreBundle\Entity\UnPressform $unpressforms)
    {
        $this->unpressforms->removeElement($unpressforms);
    }

    /**
     * Get unpressforms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnpressforms()
    {
        return $this->unpressforms;
    }
}
