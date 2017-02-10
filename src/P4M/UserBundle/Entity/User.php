<?php

namespace P4M\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Groups({"info","json"})
     */
    protected $email;

    /**
     * @var string
     * @Groups({"info", "json", "list", "donator"})
     */
    protected $username;

    /**
     *
     * @var string
     * @ORM\Column(name="name",type="string")
     * @Groups({"json"})
     */
    private $firstName;
    
    /**
     *
     * @var string
     * @ORM\Column(name="surname",type="string")
     * @Groups({"json"})
     */
    private $lastName;
    
    /**
     *
     * @var string
     * @ORM\Column(name="title",type="string", nullable=true)
     * @Groups({"json"})
     */
    private $title;
    
    
    /**
     *
     * @var string
     * @ORM\Column(name="website",type="string", nullable=true)
     * @Groups({"json"})
     */
    private $website;
    
    /**
     *
     * @var text
     * @ORM\Column(name="bio",type="text", nullable=true)
     * @Groups({"json"})
     */
    private $bio;
   
    /**
     *
     * @var text
     * @ORM\Column(name="city",type="text", nullable=true)
     * @Groups({"json"})
     */
    private $city;
    
    /**
     *
     * @var text
     * @ORM\Column(name="address",type="text", nullable=true)
     * @Groups({"json"})
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\Country")
     * @Groups({"json"})
     */
    private $country;
    
    
    
    /**
     * 
     * @ORM\Column(name="url",type="string",length=255, nullable=true)
     * @Groups({"json"})
     */
    protected $url;
    
    /**
     *@ORM\ManyToMany(targetEntity="P4M\UserBundle\Entity\Skill",cascade="persist")
     */
    private $skills;
    
    /**
     * @var object
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Wall",mappedBy="user",cascade="remove")
     */
    private $walls;
    
   
    /**
     * @var integer
     * @ORM\Column(name="wpId",type="integer",length=11, nullable=true)
     */
    private $wpId;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\UserBundle\Entity\UserLink", mappedBy="follower",cascade="all")
     */
   
    private $following;
    
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\UserBundle\Entity\UserLink", mappedBy="following",cascade="all")
     */
   
    private $followers;
    
    /**
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Image",cascade={"persist","remove"})
     * @Groups({"info","json", "donator"})
     */
    private $picture;
    
    /**
     * @ORM\OneToOne(targetEntity="P4M\UserBundle\Entity\UserPublicStatus",cascade={"persist","remove"})
     */
    private $publicStatus;
    
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Post",mappedBy="user",cascade="remove")
     *
     */
    private $posts;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Vote",mappedBy="user",cascade="remove")
     */
    private $votes;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\BackofficeBundle\Entity\ReadPostLater",mappedBy="user",cascade="remove")
     */
    private $readLater;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\NotificationBundle\Entity\Notification",mappedBy="user",cascade="remove")
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $notifications;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="termsAccepted",type="boolean")
     */
    private $termsAccepted;
    
    
     /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;
    
//    @Assert\NotNull(message="Your invitation is wrong") A remettre ici plus bas pour faire fonctionner les invitations. 
    //Solution à trouver car si activé on ne sait pas updater un user sans invitation
    /*
     * @ORM\OneToOne(targetEntity="Invitation", inversedBy="user")
     * @ORM\JoinColumn(referencedColumnName="code")
     * 
     */
//    protected $invitation;
    
    
    /**
     * @ORM\ManytoMany(targetEntity="P4M\CoreBundle\Entity\Wall",inversedBy="followers")
     */
    private $wallsFollowed;
    
    /**
     * @ORM\Column(name="dateCreated",type="datetime",nullable=true)
     */
    private $dateCreated;
    
    /**
     *  @ORM\OneToOne(targetEntity="P4M\MangoPayBundle\Entity\MangoUserNatural", mappedBy="user") 
     */
    private $mangoUserNatural;
    
    /**
     * @ORM\Column(name="birthDate",type="date",nullable=true)
     * @Groups({"json"})
     */
    private $birthDate;
    
     /**
     * @ORM\OneToOne(targetEntity="\P4M\ModerationBundle\Entity\UserFlag",mappedBy="userTarget",cascade="remove")
     */
    private $flag;
    
    /**
     * @ORM\Column(name="confirmedFlag",type="boolean",nullable=true)
     */
    private $confirmedFlag;
    
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\WallView",mappedBy="user",cascade="remove")
     */
    private $wallViews;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\TrackingBundle\Entity\PostView",mappedBy="user",cascade="remove")
     */
    private $postViews;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Comment",mappedBy="user",cascade={"persist","remove"})
     * @ORM\OrderBy({"dateAdded" = "DESC"})
     */
    private $comments;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\ModerationBundle\Entity\UserFlagConfirmation",mappedBy="user",cascade="remove")
     */
    private $confirmations;
    
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Post",mappedBy="author")
     * @Groups({"info"})
     */
    private $productions;
    
    /**
     * @ORM\OneToMany(targetEntity="P4M\CoreBundle\Entity\Pressform",mappedBy="sender")
     */
    private $sentPressforms;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="producerEnabled",type="boolean",nullable=true)
     */
    private $producerEnabled;
    
    /**
     * @ORM\Column(name="producerKey",type="string",nullable=true)
     */
    private $producerKey;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="firstLogin",type="boolean")
     */
    private $firstLogin;
    
    /**
     * @var boolean
     * @ORM\Column(name="alertWalletEmpty",type="boolean")
     */
    private $alertWalletEmpty;

    /**
     * @var string
     * @ORM\Column(name="language", type="string", options={"default":"en"}, nullable=False)
     */
    private $language = "en";

    public function __construct()
    {
        parent::__construct();
        $this->categories = array();
        $this->tags = array();
        $this->roles = array('ROLE_USER');
        $this->skills = array();
        $this->walls = new \Doctrine\Common\Collections\ArrayCollection();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->following = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dateCreated = new \DateTime();
        $this->confirmedFlag = false;
        $this->producerEnabled=false;
        $this->firstLogin=true;
        $this->alertWalletEmpty=true;
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function eraseCredentials()
    {
        
    }
    


    /**
     * Set url
     *
     * @param string $url
     * @return User
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set wpId
     *
     * @param integer $wpId
     * @return User
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
     * Add following
     *
     * @param \P4M\UserBundle\Entity\User $following
     * @return User
     */
    public function addFollowing(\P4M\UserBundle\Entity\User $following)
    {
        $this->following[] = $following;
    
        return $this;
    }

    /**
     * Remove following
     *
     * @param \P4M\UserBundle\Entity\User $following
     */
    public function removeFollowing(\P4M\UserBundle\Entity\User $following)
    {
        $this->following->removeElement($following);
    }

    /**
     * Get following
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * Add followers
     *
     * @param \P4M\UserBundle\Entity\User $followers
     * @return User
     */
    public function addFollower(\P4M\UserBundle\Entity\User $followers)
    {
//        $link = newP4M\UserBundle\Entity\UserLink();
        
        $this->followers[] = $followers;
        $followers->addFollowing($this);
    
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
     * Set title
     *
     * @param string $title
     * @return User
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
     * Set website
     *
     * @param string $website
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set bio
     *
     * @param string $bio
     * @return User
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    
        return $this;
    }

    /**
     * Get bio
     *
     * @return string 
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Add skills
     *
     * @param \P4M\UserBundle\Entity\Skill $skills
     * @return User
     */
    public function addSkill(\P4M\UserBundle\Entity\Skill $skills)
    {
        $this->skills[] = $skills;
    
        return $this;
    }

    /**
     * Remove skills
     *
     * @param \P4M\UserBundle\Entity\Skill $skills
     */
    public function removeSkill(\P4M\UserBundle\Entity\Skill $skills)
    {
        $this->skills->removeElement($skills);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkills()
    {
        return $this->skills;
    }
    
    public function setSkills(array $skills= null)
    {
        if ($skills !== null)
        {
            $this->skills = $skills;
        }
        return $this;
    }

    /**
     * Set picture
     *
     * @param \P4M\CoreBundle\Entity\Image $picture
     * @return User
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
        if ($this->picture !== null)
        {
            return $this->picture;
        }
        else
        {
            $picture = new \P4M\CoreBundle\Entity\Image();
            $picture->forceId('defaultUser');
            $picture->setName('jpg');
            return $picture;
        }
    }

    /**
     * Set publicStatus
     *
     * @param \P4M\CoreBundle\Entity\UserPublicStatus $publicStatus
     * @return User
     */
    public function setPublicStatus(\P4M\UserBundle\Entity\UserPublicStatus $publicStatus = null)
    {
        $this->publicStatus = $publicStatus;
    
        return $this;
    }

    /**
     * Get publicStatus
     *
     * @return \P4M\CoreBundle\Entity\UserPublicStatus 
     */
    public function getPublicStatus()
    {
        return $this->publicStatus;
    }

    /**
     * Set termsAccepted
     *
     * @param boolean $termsAccepted
     * @return User
     */
    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = $termsAccepted;
    
        return $this;
    }

    /**
     * Get termsAccepted
     *
     * @return boolean 
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }



    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;
    
        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;
    
        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;
    
        return $this;
    }

    /**
     * Get google_id
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;
    
        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string 
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Add votes
     *
     * @param \P4M\CoreBundle\Entity\Vote $votes
     * @return User
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
     * Add walls
     *
     * @param \P4M\CoreBundle\Entity\Wall $walls
     * @return User
     */
    public function addWall(\P4M\CoreBundle\Entity\Wall $walls)
    {
        $this->walls[] = $walls;
    
        return $this;
    }

    /**
     * Remove walls
     *
     * @param \P4M\CoreBundle\Entity\Wall $walls
     */
    public function removeWall(\P4M\CoreBundle\Entity\Wall $walls)
    {
        $this->walls->removeElement($walls);
    }

    /**
     * Get walls
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWalls()
    {
        return $this->walls;
    }

    /**
     * Add readLater
     *
     * @param \P4M\BackofficeBundle\Entity\ReadPostLater $readLater
     * @return User
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
     * Set invitation
     *
     * @param \P4M\UserBundle\Entity\Invitation $invitation
     * @return User
     */
    public function setInvitation(\P4M\UserBundle\Entity\Invitation $invitation = null)
    {
        $this->invitation = $invitation;
    
        return $this;
    }

    /**
     * Get invitation
     *
     * @return \P4M\UserBundle\Entity\Invitation 
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    
    
    
    

    /**
     * Add wallsFollowed
     *
     * @param \P4M\CoreBundle\Entity\Wall $wallsFollowed
     * @return User
     */
    public function addWallsFollowed(\P4M\CoreBundle\Entity\Wall $wallsFollowed)
    {
        $this->wallsFollowed[] = $wallsFollowed;
    
        return $this;
    }

    /**
     * Remove wallsFollowed
     *
     * @param \P4M\CoreBundle\Entity\Wall $wallsFollowed
     */
    public function removeWallsFollowed(\P4M\CoreBundle\Entity\Wall $wallsFollowed)
    {
        $this->wallsFollowed->removeElement($wallsFollowed);
    }

    /**
     * Get wallsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWallsFollowed()
    {
        return $this->wallsFollowed;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Add notifications
     *
     * @param \P4M\NotificationBundle\Entity\Notification $notifications
     * @return User
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
    
    
    public function hasFollower(User $user)
    {
        return $this->followers->contains($user);
    }
    

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set mangoUserNatural
     *
     * @param \P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUserNatural
     * @return User
     */
    public function setMangoUserNatural(\P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUserNatural = null)
    {
        $this->mangoUserNatural = $mangoUserNatural;
    
        return $this;
    }

    /**
     * Get mangoUserNatural
     *
     * @return \P4M\MangoPayBundle\Entity\MangoUserNatural 
     */
    public function getMangoUserNatural()
    {
        return $this->mangoUserNatural;
    }

    /**
     * Set country
     *
     * @param \P4M\UserBundle\Entity\Country $country
     * @return User
     */
    public function setCountry(\P4M\UserBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \P4M\UserBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }
    
    public function getWallsCount()
    {
        return $this->walls->count();
    }
    public function getPostsCount()
    {
        return $this->posts->count();
    }

    /**
     * Set confirmedFlag
     *
     * @param boolean $confirmedFlag
     * @return User
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
     * Add posts
     *
     * @param \P4M\CoreBundle\Entity\Post $posts
     * @return User
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
     * Set flag
     *
     * @param \P4M\ModerationBundle\Entity\UserFlag $flag
     * @return User
     */
    public function setFlag(\P4M\ModerationBundle\Entity\UserFlag $flag = null)
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    public function getSurName()
    {
        return $this->lastName;
    }
    
    public function getName()
    {
        return $this->firstName;
    }

    /**
     * Add wallViews
     *
     * @param \P4M\TrackingBundle\Entity\WallView $wallViews
     * @return User
     */
    public function addWallView(\P4M\TrackingBundle\Entity\WallView $wallViews)
    {
        $this->wallViews[] = $wallViews;
    
        return $this;
    }

    /**
     * Remove wallViews
     *
     * @param \P4M\TrackingBundle\Entity\WallView $wallViews
     */
    public function removeWallView(\P4M\TrackingBundle\Entity\WallView $wallViews)
    {
        $this->wallViews->removeElement($wallViews);
    }

    /**
     * Get wallViews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWallViews()
    {
        return $this->wallViews;
    }

    /**
     * Add comments
     *
     * @param \P4M\CoreBundle\Entity\Comment $comments
     * @return User
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
     * Add confirmations
     *
     * @param \P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations
     * @return User
     */
    public function addConfirmation(\P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations)
    {
        $this->confirmations[] = $confirmations;
    
        return $this;
    }

    /**
     * Remove confirmations
     *
     * @param \P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations
     */
    public function removeConfirmation(\P4M\ModerationBundle\Entity\UserFlagConfirmation $confirmations)
    {
        $this->confirmations->removeElement($confirmations);
    }

    /**
     * Get confirmations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfirmations()
    {
        return $this->confirmations;
    }

    /**
     * Add postViews
     *
     * @param \P4M\TrackingBundle\Entity\PostView $postViews
     * @return User
     */
    public function addPostView(\P4M\TrackingBundle\Entity\PostView $postViews)
    {
        $this->postViews[] = $postViews;
    
        return $this;
    }

    /**
     * Remove postViews
     *
     * @param \P4M\TrackingBundle\Entity\PostView $postViews
     */
    public function removePostView(\P4M\TrackingBundle\Entity\PostView $postViews)
    {
        $this->postViews->removeElement($postViews);
    }

    /**
     * Get postViews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPostViews()
    {
        return $this->postViews;
    }

    /**
     * Add productions
     *
     * @param \P4M\Corebundle\Entity\Post $productions
     * @return User
     */
    public function addProduction(\P4M\Corebundle\Entity\Post $productions)
    {
        $this->productions[] = $productions;
    
        return $this;
    }

    /**
     * Remove productions
     *
     * @param \P4M\Corebundle\Entity\Post $productions
     */
    public function removeProduction(\P4M\Corebundle\Entity\Post $productions)
    {
        $this->productions->removeElement($productions);
    }

    /**
     * Get productions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductions()
    {
        return $this->productions;
    }

    /**
     * Add sentPressforms
     *
     * @param \P4M\Corebundle\Entity\Pressform $sentPressforms
     * @return User
     */
    public function addSentPressform(\P4M\Corebundle\Entity\Pressform $sentPressforms)
    {
        $this->sentPressforms[] = $sentPressforms;
    
        return $this;
    }

    /**
     * Remove sentPressforms
     *
     * @param \P4M\Corebundle\Entity\Pressform $sentPressforms
     */
    public function removeSentPressform(\P4M\Corebundle\Entity\Pressform $sentPressforms)
    {
        $this->sentPressforms->removeElement($sentPressforms);
    }

    /**
     * Get sentPressforms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSentPressforms()
    {
        return $this->sentPressforms;
    }
    
    public function getUnpayedSentPressforms()
    {
        $unpayed = [];
        foreach($this->sentPressforms as $pressform)
        {
            if (!$pressform->getPayed())
            {
                $unpayed[] = $pressform;
            }
        }
        return $unpayed;
    }
    

    /**
     * Set producerEnabled
     *
     * @param boolean $producerEnabled
     * @return User
     */
    public function setProducerEnabled($producerEnabled)
    {
        $this->producerEnabled = $producerEnabled;
    
        return $this;
    }

    /**
     * Get producerEnabled
     *
     * @return boolean 
     */
    public function getProducerEnabled()
    {
        return $this->producerEnabled;
    }

    /**
     * Set producerKey
     *
     * @param string $producerKey
     * @return User
     */
    public function setProducerKey($producerKey)
    {
        $this->producerKey = $producerKey;
    
        return $this;
    }

    /**
     * Get producerKey
     *
     * @return string 
     */
    public function getProducerKey()
    {
        return $this->producerKey;
    }

    /**
     * Set firstLogin
     *
     * @param boolean $firstLogin
     * @return User
     */
    public function setFirstLogin($firstLogin)
    {
        $this->firstLogin = $firstLogin;
    
        return $this;
    }

    /**
     * Get firstLogin
     *
     * @return boolean 
     */
    public function getFirstLogin()
    {
        return $this->firstLogin;
    }

    /**
     * Set alertWalletEmpty
     *
     * @param boolean $alertWalletEmpty
     * @return User
     */
    public function setAlertWalletEmpty($alertWalletEmpty)
    {
//        if (null != $alertWalletEmpty)
//        {
            $this->alertWalletEmpty = $alertWalletEmpty;
//        }
        
    
        return $this;
    }

    /**
     * Get alertWalletEmpty
     *
     * @return boolean 
     */
    public function getAlertWalletEmpty()
    {
        return $this->alertWalletEmpty;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
