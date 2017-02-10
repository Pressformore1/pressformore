<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccount
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\MangoPayBundle\Entity\BankAccountRepository")
 */
class BankAccount
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
     * @ORM\Column(name="BankName", type="string", length=255)
     */
    private $bankName;

    /**
     * @var string
     *
     * @ORM\Column(name="InstitutionNumber", type="integer", nullable=true)
     */
    private $institutionNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="BranchCode", type="integer", nullable=true)
     */
    private $branchCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="AccountNumber", type="integer", nullable=true)
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="IBAN", type="string", length=255, nullable=true)
     */
    private $iBAN;

    /**
     * @var string
     *
     * @ORM\Column(name="BIC", type="string", length=255, nullable=true)
     */
    private $bIC;

    /**
     * @var integer
     *
     * @ORM\Column(name="ABA", type="integer", nullable=true)
     */
    private $aBA;

    /**
     * @var integer
     *
     * @ORM\Column(name="SortCode", type="integer", nullable=true)
     */
    private $sortCode;

    /**
     * @var string
     *
     * @ORM\Column(name="OwnerName", type="string", length=255)
     */
    private $ownerName;

    /**
     * @var string
     *
     * @ORM\Column(name="OwnerAddress", type="string", length=255)
     */
    private $ownerAddress;
    
    /**
     *
     * @var type 
     * @ORM\ManyToOne(targetEntity="P4M\MangoPayBundle\Entity\MangoUserNatural")
     */
    private $mangoUser;
    
    /**
     *
     * @var type 
     * @ORM\ManyToOne(targetEntity="P4M\UserBundle\Entity\Country")
     */
    private $country;


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
     * @return BankAccount
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
     * @param string $institutionNumber
     * @return BankAccount
     */
    public function setInstitutionNumber($institutionNumber)
    {
        $this->institutionNumber = $institutionNumber;
    
        return $this;
    }

    /**
     * Get institutionNumber
     *
     * @return string 
     */
    public function getInstitutionNumber()
    {
        return $this->institutionNumber;
    }

    /**
     * Set branchCode
     *
     * @param integer $branchCode
     * @return BankAccount
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
     * @return BankAccount
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
     * Set iBAN
     *
     * @param string $iBAN
     * @return BankAccount
     */
    public function setIBAN($iBAN)
    {
        $this->iBAN = $iBAN;
    
        return $this;
    }

    /**
     * Get iBAN
     *
     * @return string 
     */
    public function getIBAN()
    {
        return $this->iBAN;
    }

    /**
     * Set bIC
     *
     * @param string $bIC
     * @return BankAccount
     */
    public function setBIC($bIC)
    {
        $this->bIC = $bIC;
    
        return $this;
    }

    /**
     * Get bIC
     *
     * @return string 
     */
    public function getBIC()
    {
        return $this->bIC;
    }

    /**
     * Set aBA
     *
     * @param integer $aBA
     * @return BankAccount
     */
    public function setABA($aBA)
    {
        $this->aBA = $aBA;
    
        return $this;
    }

    /**
     * Get aBA
     *
     * @return integer 
     */
    public function getABA()
    {
        return $this->aBA;
    }

    /**
     * Set sortCode
     *
     * @param integer $sortCode
     * @return BankAccount
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
     * @return BankAccount
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
     * @return BankAccount
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
     * Set mangoUser
     *
     * @param \P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUser
     * @return BankAccount
     */
    public function setMangoUser(\P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUser = null)
    {
        $this->mangoUser = $mangoUser;
    
        return $this;
    }

    /**
     * Get mangoUser
     *
     * @return \P4M\MangoPayBundle\Entity\MangoUserNatural 
     */
    public function getMangoUser()
    {
        return $this->mangoUser;
    }
    
    public function getType()
    {
        if (strlen($this->institutionNumber))
        {
            return 'CA';
        }
        elseif (strlen($this->iBAN))
        {
            return 'IBAN';
        }
        elseif (strlen($this->aBA))
        {
            return 'US';
        }
        elseif (strlen($this->sortCode))
        {
            return 'BG';
        }
        else
        {
            return 'OTHER';
        }
        
    }
    
    public function hydrate(\P4M\MangoPayBundle\MangoPaySDK\Entities\BankAccount $mangoAccount)
    {
        if (isset($mangoAccount->Details->ABA))
        {
            $this->setABA($mangoAccount->Details->ABA);
        }
        if (isset($mangoAccount->Details->AccountNumber))
        {
            $this->setAccountNumber($mangoAccount->Details->AccountNumber);
        }
        if (isset($mangoAccount->Details->BIC))
        {
            $this->setBIC($mangoAccount->Details->BIC);
        }
        if (isset($mangoAccount->Details->BankName))
        {
            $this->setBankName($mangoAccount->Details->BankName);
        }
        if (isset($mangoAccount->Details->BranchCode))
        {
            $this->setBranchCode($mangoAccount->Details->BranchCode);
        }
        
        if (isset($mangoAccount->Details->IBAN))
        {
            $this->setIBAN($mangoAccount->Details->IBAN);
        }
        
        if (isset($mangoAccount->Details->InstitutionNumber))
        {
            $this->setInstitutionNumber($mangoAccount->Details->InstitutionNumber);
        }
        if (isset($mangoAccount->Details->SortCode))
        {
            $this->setSortCode($mangoAccount->Details->SortCode);
        }
        
        
        $this->setOwnerAddress($mangoAccount->OwnerAddress);
        $this->setOwnerName($mangoAccount->OwnerName);
        $this->id = $mangoAccount->Id;
        
    }

    /**
     * Set country
     *
     * @param \P4M\UserBundle\Entity\Country $country
     * @return BankAccount
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
}
