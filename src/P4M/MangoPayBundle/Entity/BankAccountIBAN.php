<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * BankAccountIBAN
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BankAccountIBAN
{
    const TYPE = 'IBAN';
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

//    Old rotten validation @Assert\Length(min=16,max=16)
    /**
     * @var string
     *
     * @ORM\Column(name="iban", type="string", length=255)
     * @Assert\Regex("#[a-zA-Z]{2}[0-9]{2}[a-zA-Z0-9]{4}[0-9]{7}([a-zA-Z0-9]?){0,16}#")
     */
    private $iban;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255)
     */
    private $bic;

    /**
     * @var string
     *
     * @ORM\Column(name="ownerName", type="string", length=255)
     */
    private $ownerName;

    /**
     * @var string
     *
     * @ORM\Column(name="ownerAddress", type="string", length=255)
     */
    private $ownerAddress;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="P4M\MangoPayBundle\Entity\MangoUserNatural")
     */
    private $mangoUser;

    /**
     *
     * @var strings
     * @ORM\Column(name="tag",type="string",length=128)
     */
    private $tag;


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
     * Set iban
     *
     * @param string $iban
     * @return BankAccountIBAN
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    
        return $this;
    }

    /**
     * Get iban
     *
     * @return string 
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * Set bic
     *
     * @param string $bic
     * @return BankAccountIBAN
     */
    public function setBic($bic)
    {
        $this->bic = $bic;
    
        return $this;
    }

    /**
     * Get bic
     *
     * @return string 
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * Set ownerName
     *
     * @param string $ownerName
     * @return BankAccountIBAN
     */
    public function setOwnerName($ownerName)
    {
        $this->ownerName = $ownerName;
    
        return $this;
    }

    /**
     * Get ownerName
     *
     * @return string 
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * Set ownerAddress
     *
     * @param string $ownerAddress
     * @return BankAccountIBAN
     */
    public function setOwnerAddress($ownerAddress)
    {
        $this->ownerAddress = $ownerAddress;
    
        return $this;
    }

    /**
     * Get ownerAddress
     *
     * @return string 
     */
    public function getOwnerAddress()
    {
        return $this->ownerAddress;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mangoUser = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get mangoUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMangoUser()
    {
        return $this->mangoUser;
    }

    /**
     * Set mangoUser
     *
     * @param \P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUser
     * @return BankAccountIBAN
     */
    public function setMangoUser(\P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUser = null)
    {
        $this->mangoUser = $mangoUser;
    
        return $this;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return BankAccountIBAN
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
}

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }
}