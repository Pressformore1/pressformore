<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccountCA
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BankAccountCA
{
    
    const TYPE = 'CA';
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
     * @ORM\Column(name="bankName", type="string", length=255)
     */
    private $bankName;

    /**
     * @var integer
     *
     * @ORM\Column(name="institutionNumber", type="integer")
     */
    private $institutionNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="branchCode", type="integer")
     */
    private $branchCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="accountNumber", type="integer")
     */
    private $accountNumber;

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
     * Set bankName
     *
     * @param string $bankName
     * @return BankAccountCA
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    
        return $this;
    }

    /**
     * Get bankName
     *
     * @return string 
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set institutionNumber
     *
     * @param integer $institutionNumber
     * @return BankAccountCA
     */
    public function setInstitutionNumber($institutionNumber)
    {
        $this->institutionNumber = $institutionNumber;
    
        return $this;
    }

    /**
     * Get institutionNumber
     *
     * @return integer 
     */
    public function getInstitutionNumber()
    {
        return $this->institutionNumber;
    }

    /**
     * Set branchCode
     *
     * @param integer $branchCode
     * @return BankAccountCA
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;
    
        return $this;
    }

    /**
     * Get branchCode
     *
     * @return integer 
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * Set accountNumber
     *
     * @param integer $accountNumber
     * @return BankAccountCA
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
     * Set ownerName
     *
     * @param string $ownerName
     * @return BankAccountCA
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
     * @return BankAccountCA
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