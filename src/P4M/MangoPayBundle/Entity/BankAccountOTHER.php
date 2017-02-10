<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccountOTHER
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class BankAccountOTHER
{
    const TYPE = 'OTHER';
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
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="bic", type="string", length=255)
     */
    private $bic;

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
     * Set country
     *
     * @param string $country
     * @return BanckAccountOTHER
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set bic
     *
     * @param string $bic
     * @return BanckAccountOTHER
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
     * Set accountNumber
     *
     * @param integer $accountNumber
     * @return BanckAccountOTHER
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
     * @return BanckAccountOTHER
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
     * @return BanckAccountOTHER
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
