<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WalletFill
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\MangoPayBundle\Entity\WalletFillRepository")
 */
class WalletFill
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
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFill", type="date")
     */
    private $dateFill;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateExpire", type="date")
     */
    private $dateExpire;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recurrent", type="boolean")
     */
    private $recurrent;
    /**
     * @var boolean
     *
     * @ORM\Column(name="expired", type="boolean")
     */
    private $expired;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="\P4M\UserBundle\Entity\User");
     */
    private $user;
    
    /**
     * @var string
     * @ORM\Column(name="cardId", type="string")
     */
    private $cardId;
    
    /**
     * @var integer
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;


    
    public function __construct()
    {
        $this->date = new \DateTime();
        $this->dateFill = new \DateTime();
        $this->forceDateExpire();
        $this->expired = false;
    }
    
    private function forceDateExpire($expirationLimit = '+1 month',$changeFill = true)
    {
        $this->dateExpire = new \DateTime();
        $this->dateExpire->modify($expirationLimit);
        if ($changeFill)
        {
            $this->dateFill = new \DateTime();
        }
        
    }
    
    public function update($expirationLimit = '+1 month',$changeFill = true)
    {
//        if ($this->recurrent)
//        {
            $this->forceDateExpire($expirationLimit,$changeFill);
//        }
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
     * @return WalletFill
     */
    public function setDate($date)
    {
        $this->dateExpire = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->dateExpire;
    }

    /**
     * Set recurrent
     *
     * @param boolean $recurrent
     * @return WalletFill
     */
    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;
    
        return $this;
    }

    /**
     * Get recurrent
     *
     * @return boolean 
     */
    public function getRecurrent()
    {
        return $this->recurrent;
    }

    /**
     * Set dateExpire
     *
     * @param \DateTime $dateExpire
     * @return WalletFill
     */
    public function setDateExpire($dateExpire)
    {
        $this->dateExpire = $dateExpire;
    
        return $this;
    }

    /**
     * Get dateExpire
     *
     * @return \DateTime 
     */
    public function getDateExpire()
    {
        return $this->dateExpire;
    }

    /**
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return WalletFill
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
     * Set cardId
     *
     * @param string $cardId
     * @return WalletFill
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
    
        return $this;
    }

    /**
     * Get cardId
     *
     * @return string 
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return WalletFill
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set dateFill
     *
     * @param \DateTime $dateFill
     * @return WalletFill
     */
    public function setDateFill($dateFill)
    {
        $this->dateFill = $dateFill;
    
        return $this;
    }

    /**
     * Get dateFill
     *
     * @return \DateTime 
     */
    public function getDateFill()
    {
        return $this->dateFill;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     * @return WalletFill
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
    
        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }
}