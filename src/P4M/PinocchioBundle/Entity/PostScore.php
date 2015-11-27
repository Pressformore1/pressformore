<?php

namespace P4M\PinocchioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostScore
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\PinocchioBundle\Entity\PostScoreRepository")
 */
class PostScore
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
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     *
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Post",inversedBy="score")
     */
    private $post;
    
    
    private $_reach;
    private $_comment;
    private $_paid;
    private $_like;
    private $_dislike;
    private $_ratio;
    private $_share_in;
    private $_share_out;
    private $_share;
    private $_time;
    
    
    const LVL1 = 1.2;
    const LVL2 = 1.5;
    const LVL3 = 2;
    const LVL4 = 2.5;
    const LVL5 = 3;

    const NIV1 = 3;
    const NIV2 = 2;
    const NIV3 = 1;
    
    


    
    public function __construct()
    {
        $this->setDate(new \DateTime());
    }
    
    
    public function hydrate()
    {
        if ($this->getPost() !== null)
        {
            $this->_reach = count($this->getPost()->getViews());
            if ($this->_reach == 0)
            {
                $this->_reach = 1;
            }
            $this->_comment = count($this->getPost()->getComments());
             if ($this->_comment == 0)
            {
                $this->_comment = 1;
            }
            $this->_paid = 1; // ToDo
            $this->_like = $this->getPost()->getPositiveVoteNumber();
             if ($this->_like == 0)
            {
                $this->_like = 1;
            }
            $this->_dislike = $this->getPost()->getNegativeVoteNumber();
            if ($this->_dislike == 0)
            {
                $this->_dislike = 1;
            }
            $this->_share_in = 1; // ToDo
            $this->_share_out = 1; // ToDo
            $this->_time = $this->getPost()->getDateAdded()->getTimestamp();
        }
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
     * Set score
     *
     * @param integer $score
     * @return PostScore
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return PostScore
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
     * @return PostScore
     */
    public function setPost(\P4M\CoreBundle\Entity\Post $post = null)
    {
        $this->post = $post;
        $this->post->setScore($this);
        $this->hydrate();
        
    
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
    
    
    private function setRatio() {
        $this->_ratio = $this->_like/$this->_dislike;   
    }
    
    public function getRatio()
    {
        return $this->_ratio;
    }
    
    public function generateScore() {
        $this->hydrate();
        $this->exe();        
        $x = self::LVL1*\log($this->_reach) + self::LVL2*\log($this->_comment) + self::LVL3*\log($this->_paid) + self::LVL4*\log($this->_ratio) + \log(self::LVL5*$this->_share) + $this->_time; 
        $this->score = $x;
        return $x;
    }
    
    private function setShare() {
        $this->_share =  0.5*$this->_share_in + 1.5*$this->_share_out;
    }
    
    private function setTime() {
        $current_time = time();
        
        $age = $current_time-$this->_time;
        
        
//            if($age <= 60*60*3) {
//                $this->_time = \log($this->_time)+$age;
//            } else {
             $this->_time = \log($this->_time)-\log($age);  
//            }
    }   
    
    private function exe() {
        $this->setRatio();   
        $this->setShare();
        $this->setTime();
    }
    

    
}