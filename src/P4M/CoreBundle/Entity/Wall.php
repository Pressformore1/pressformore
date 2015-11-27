<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use P4M\CoreBundle\Entity\Image;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Wall
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\WallRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Wall
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * 
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime")
     */
    private $dateUpdate;

    /**
     *
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\User",inversedBy="walls")
     */
    private $user;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Category")
     * @ORM\JoinTable(name="wall_categories_enabled")  
     */
    private $includedCategories;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Tag")
     * @ORM\JoinTable(name="wall_tags_enabled")
     */
    private $includedTags;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\UserBundle\Entity\User")
     * @ORM\JoinTable(name="wall_users_enabled")
     */
    private $includedPeople;

    
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Category")
     * @ORM\JoinTable(name="wall_categories_disabled")
     */
    private $excludedCategories;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\CoreBundle\Entity\Tag")
     * @ORM\JoinTable(name="wall_tags_disabled")
     */
    private $excludedTags;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\UserBundle\Entity\User")
     * @ORM\JoinTable(name="wall_user_disabled")
     */
    private $excludedPeople;

    /**
    * * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="relationField", value="user"),
     *          @Gedmo\SlugHandlerOption(name="relationSlugField", value="username"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/")
     *      })
     * }, separator="-", updatable=true, fields={"name"})
    * @ORM\Column(length=128, unique=true)
    */
    private $slug;
            
            
    /**
     *
     * @ORM\ManyToMany(targetEntity="P4M\UserBundle\Entity\User",mappedBy="wallsFollowed")
     */
    private $followers;
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Vote",mappedBy="wall",cascade="remove")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $votes;
    /**
     *
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Comment",mappedBy="wall",cascade={"persist","remove"})
     * @ORM\OrderBy({"dateAdded" = "DESC"})
     */
    private $comments;
    
    /**
     * @var text
     * @ORM\Column(name="description",type="text",nullable=true)
     * 
     */
    private $description;
    
    /**
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Image",cascade={"persist","remove"})
     * @Assert\Valid
     */
    private $picture;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\WallView",mappedBy="wall",cascade="remove")
     */
    private $views;
    
    
     /**
     * @ORM\OneToOne(targetEntity="\P4M\ModerationBundle\Entity\WallFlag",mappedBy="wall",cascade="remove")
     */
    private $flag;
    
    /**
     * @ORM\Column(name="confirmedFlag",type="boolean",nullable=true)
     */
    private $confirmedFlag;
    
    /**
     * @ORM\OneToMany(targetEntity="\P4M\TrackingBundle\Entity\UserActivity",mappedBy="wall",cascade="remove")
     */
    private $activities;
   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateAdded = new \DateTime();
        $this->dateUpdate = new \DateTime();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->includedCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->includedTags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->includedPeople = new \Doctrine\Common\Collections\ArrayCollection();
        $this->excludedCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->excludedTags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->views = new \Doctrine\Common\Collections\ArrayCollection();
        $this->excludedPeople = new \Doctrine\Common\Collections\ArrayCollection();
        $this->picture = new Image();
        $this->confirmedFlag = false;
        $this->votes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Wall
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Wall
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
    public function getDateAddedTimeStamp()
    {
        return $this->dateAdded->getTimestamp();
    }
   
    
    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return Wall
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
     * Add includedCategories
     *
     * @param \P4M\CoreBundle\Entity\Category $includedCategories
     * @return Wall
     */
    public function addIncludedCategorie(\P4M\CoreBundle\Entity\Category $includedCategories)
    {
        $this->includedCategories[] = $includedCategories;
    
        return $this;
    }

    /**
     * Remove includedCategories
     *
     * @param \P4M\CoreBundle\Entity\Category $includedCategories
     */
    public function removeIncludedCategorie(\P4M\CoreBundle\Entity\Category $includedCategories)
    {
        $this->includedCategories->removeElement($includedCategories);
    }

    /**
     * Get includedCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncludedCategories()
    {
        return $this->includedCategories;
    }
    public function getIncludedCatsId()
    {
        $ids = [];
        
        foreach ($this->includedCategories as $cat )
        {
            $ids[]=$cat->getId();
        }
        return $ids;
    }
    
    /**
     * Set includedCategories
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setincludedCategories($includedCategories)
    {
        $this->includedCategories = $includedCategories;
        return $this;
    }

    /**
     * Add includedTags
     *
     * @param \P4M\CoreBundle\Entity\Tag $includedTags
     * @return Wall
     */
    public function addIncludedTag(\P4M\CoreBundle\Entity\Tag $includedTags)
    {
        $this->includedTags[] = $includedTags;
    
        return $this;
    }

    /**
     * Remove includedTags
     *
     * @param \P4M\CoreBundle\Entity\Tag $includedTags
     */
    public function removeIncludedTag(\P4M\CoreBundle\Entity\Tag $includedTags)
    {
        $this->includedTags->removeElement($includedTags);
    }

    /**
     * Get includedTags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncludedTags()
    {
        return $this->includedTags;
    }
    
    public function getIncludedTagsId()
    {
        $ids = [];
        
        foreach ($this->includedTags as $tag )
        {
            $ids[]=$tag->getId();
        }
        return $ids;
    }

    /**
     * Set includedTags
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setincludedTags($includedTags)
    {
        $this->includedTags = $includedTags;
        return $this;
    }
    /**
     * Add includedPeople
     *
     * @param \P4M\UserBundle\Entity\User $includedPeople
     * @return Wall
     */
    public function addIncludedPeople(\P4M\UserBundle\Entity\User $includedPeople)
    {
        $this->includedPeople[] = $includedPeople;
    
        return $this;
    }

    /**
     * Remove includedPeople
     *
     * @param \P4M\UserBundle\Entity\User $includedPeople
     */
    public function removeIncludedPeople(\P4M\UserBundle\Entity\User $includedPeople)
    {
        $this->includedPeople->removeElement($includedPeople);
    }

    /**
     * Get includedPeople
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncludedPeople()
    {
        return $this->includedPeople;
    }
    
    /**
     * Set includedPeople
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setincludedPeople($includedPeople)
    {
        $this->includedPeople = $includedPeople;
        return $this;
    }

    /**
     * Add excludedCategories
     *
     * @param \P4M\CoreBundle\Entity\Category $excludedCategories
     * @return Wall
     */
    public function addExcludedCategorie(\P4M\CoreBundle\Entity\Category $excludedCategories)
    {
        $this->excludedCategories[] = $excludedCategories;
    
        return $this;
    }

    /**
     * Remove excludedCategories
     *
     * @param \P4M\CoreBundle\Entity\Category $excludedCategories
     */
    public function removeExcludedCategorie(\P4M\CoreBundle\Entity\Category $excludedCategories)
    {
        $this->excludedCategories->removeElement($excludedCategories);
    }

    /**
     * Get excludedCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExcludedCategories()
    {
        return $this->excludedCategories;
    }
    
    public function getExcludedCatsId()
    {
        $ids = [];
        
        foreach ($this->excludedCategories as $cat )
        {
            $ids[]=$cat->getId();
        }
        return $ids;
    }
    
    /**
     * Set excludedCategories
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setExcludedCategories($excludedCategories)
    {
        $this->excludedCategories = $excludedCategories;
        return $this;
    }

    /**
     * Add excludedTags
     *
     * @param \P4M\CoreBundle\Entity\Tag $excludedTags
     * @return Wall
     */
    public function addExcludedTag(\P4M\CoreBundle\Entity\Tag $excludedTags)
    {
        $this->excludedTags[] = $excludedTags;
    
        return $this;
    }

    /**
     * Remove excludedTags
     *
     * @param \P4M\CoreBundle\Entity\Tag $excludedTags
     */
    public function removeExcludedTag(\P4M\CoreBundle\Entity\Tag $excludedTags)
    {
        $this->excludedTags->removeElement($excludedTags);
    }

    /**
     * Get excludedTags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExcludedTags()
    {
        return $this->excludedTags;
    }
    
    public function getExcludedTagsId()
    {
        $ids = [];
        
        foreach ($this->excludedTags as $tag )
        {
            $ids[]= $tag->getId();
        }
        return $ids;
    }
    
    /**
     * Set excludedTags
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setExcludedTags($excludedTags)
    {
        $this->excludedTags = $excludedTags;
        return $this;
    }

    /**
     * Add excludedPeople
     *
     * @param \P4M\UserBundle\Entity\User $excludedPeople
     * @return Wall
     */
    public function addExcludedPeople(\P4M\UserBundle\Entity\User $excludedPeople)
    {
        $this->excludedPeople[] = $excludedPeople;
    
        return $this;
    }

    /**
     * Remove excludedPeople
     *
     * @param \P4M\UserBundle\Entity\User $excludedPeople
     */
    public function removeExcludedPeople(\P4M\UserBundle\Entity\User $excludedPeople)
    {
        $this->excludedPeople->removeElement($excludedPeople);
    }

    /**
     * Get excludedPeople
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExcludedPeople()
    {
        return $this->excludedPeople;
    }
    
    /**
     * Set excludedTags
     *
     * @return \P4M\CoreBundle\Entity\Wall
     */
    public function setExcludedPeople($excludedPeople)
    {
        $this->excludedPeople = $excludedPeople;
        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Wall
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
     * Add followers
     *
     * @param \P4M\UserBundle\Entity\User $followers
     * @return Wall
     */
    public function addFollower(\P4M\UserBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;
    
        return $this;
    }

    /**
     * Remove followers
     *
     * @param \P4M\UserBundle\Entity\User $followers
     */
    public function removeFollower(\P4M\UserBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
    }
    
    public function hasFollower(\P4M\UserBundle\Entity\User $user = null)
    {
        if (null === $user)
        {
            return false;
        }
//        return 'false';
        return $this->followers->contains($user);
        
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add votes
     *
     * @param \P4M\CoreBundle\Entity\Vote $votes
     * @return Wall
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
    
    public function getVotesRatio()
    {
        if ($this->getNegativeVoteNumber() == 0)
        {
            return 1;
        }
        elseif($this->getPositiveVoteNumber()==0)
        {
            return 0;
        }
        else
        {
            return count($this->votes)/$this->getPositiveVoteNumber();
        }
    }

    /**
     * Add comments
     *
     * @param \P4M\CoreBundle\Entity\Comment $comments
     * @return Wall
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
     * Set description
     *
     * @param string $description
     * @return Wall
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

    /**
     * Set picture
     *
     * @param \P4M\CoreBundle\Entity\Image $picture
     * @return Wall
     */
    public function setPicture(\P4M\CoreBundle\Entity\Image $picture = null)
    {
        $this->picture = $picture;
    
        return $this;
    }

    /**
     * Get picture
     *
     * @return \P4M\CoreBundle\Entity\Image 
     */
    public function getPicture()
    {
        if (null !== $this->picture)
        {
            return $this->picture;
        }
        else
        {
            $picture = new Image();
            $picture->forceId('default-strew');
            $picture->setName('jpg');
            
            
            return $picture;
        }
        
    }

    /**
     * Add views
     *
     * @param \P4M\TrackingBundle\Entity\WallView $views
     * @return Wall
     */
    public function addView(\P4M\TrackingBundle\Entity\WallView $views)
    {
        $this->views[] = $views;
    
        return $this;
    }

    /**
     * Remove views
     *
     * @param \P4M\TrackingBundle\Entity\WallView $views
     */
    public function removeView(\P4M\TrackingBundle\Entity\WallView $views)
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
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return Wall
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
    
        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }
    
    /**
    * @ORM\PrePersist
    */
    private function forceDate()
    {
        $this->setDateUpdate(new \DateTime());
    }
    
    public function isSearchable()
    {
       return true;
    }
    public function getFollowersCount()
    {
        return $this->followers->count();
    }
    public function getViewsCount()
    {
        return $this->views->count();
    }
    public function getCommentsCount()
    {
        return $this->comments->count();
    }

    /**
     * Set confirmedFlag
     *
     * @param boolean $confirmedFlag
     * @return Wall
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

    /**
     * Set flag
     *
     * @param \P4M\ModerationBundle\Entity\PostFlag $flag
     * @return Wall
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
    
    public function __toString()
    {
        return $this->user->getUsername().' '.$this->name;
    }
}