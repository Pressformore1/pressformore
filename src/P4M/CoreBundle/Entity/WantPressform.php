<?php

namespace P4M\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * WantPressform
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\CoreBundle\Entity\WantPressformRepository")
 */
class WantPressform
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
     * @var \string
     *
     * @ORM\Column(name="twitter", type="string",nullable=true)
     */
    private $twitter;
    /**
     * @var \string
     *
     * @ORM\Column(name="email", type="string",nullable=true)
     */
    private $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="\P4M\CoreBundle\Entity\Post",inversedBy="wantPressforms")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $post;
    
    /**
     * @Groups({"donator"})
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;




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
     * @return WantPressform
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
     * Set post
     *
     * @param \P4M\CoreBundle\Entity\Post $post
     * @return WantPressform
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
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WantPressform
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
     * Set twitter
     *
     * @param string $twitter
     * @return WantPressform
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    
        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return WantPressform
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
     * Set url
     *
     * @param string $url
     * @return WantPressform
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
     * @Assert\Callback
     * @param WantPressform $object
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public static function validate(WantPressform $object, ExecutionContextInterface $context, $payload)
    {
            if(!$object->getPost() AND !$object->getUrl()){
                $context->buildViolation('Vous devez vouloir rémunerer un article')
                    ->atPath('post')
                    ->addViolation();
            }
    }

}
