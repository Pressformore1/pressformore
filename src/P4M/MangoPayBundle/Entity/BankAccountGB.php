<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccountGB
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BankAccountGB
{
    const TYPE = 'GB';
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
     * @ORM\Column(name="sortCode", type="integer")
     */
    private $sortCode;

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
     * @return BankAccountGB
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
     * Set sortCode
     *
     * @param integer $sortCode
     * @return BankAccountGB
     */
    public function setSortCode($sortCode)
    {
        $this->sortCode = $sortCode;
    
        return $this;
    }

    /**
     * Get sortCode
     *
     * @return integer 
     */
    public function getSortCode()
    {
        return $this->sortCode;
    }

    /**
     * Set ownerName
     *
     * @param string $ownerName
     * @return BankAccountGB
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
     * @return BankAccountGB
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
