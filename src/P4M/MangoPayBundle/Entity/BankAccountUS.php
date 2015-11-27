<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccountUS
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BankAccountUS
{
    const TYPE = 'US';
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
     * @ORM\Column(name="accountNumber", type="integer")
     */
    private $accountNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="aba", type="integer")
     */
    private $aba;

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
    
    private $mangoUser;


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
     * Set accountNumber
     *
     * @param integer $accountNumber
     * @return BankAccountUS
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    
        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return integer 
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set aba
     *
     * @param integer $aba
     * @return BankAccountUS
     */
    public function setAba($aba)
    {
        $this->aba = $aba;
    
        return $this;
    }

    /**
     * Get aba
     *
     * @return integer 
     */
    public function getAba()
    {
        return $this->aba;
    }

    /**
     * Set ownerName
     *
     * @param string $ownerName
     * @return BankAccountUS
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
     * @return BankAccountUS
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
}