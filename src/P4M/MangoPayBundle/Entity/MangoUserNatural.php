<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MangoUserNatural
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="P4M\MangoPayBundle\Entity\MangoUserNaturalRepository")
 */
class MangoUserNatural
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

   
    private $firstName;

   
    private $lastName;

   
    private $address;

    
    private $birthday;
    
    private $nationality;
    private $countryOfResidence;

    /**
     * @var string
     *
     * @ORM\Column(name="Occupation", type="string", length=255,nullable=true)
     */
    private $occupation;

    /**
     * @var integer
     *
     * @ORM\Column(name="IncomeRange", type="integer",nullable=true)
     */
    private $incomeRange;

    /**
     * @var string
     *
     * @ORM\Column(name="ProofOfIdentity", type="string", length=255,nullable=true)
     */
    private $proofOfIdentity;

    /**
     * @var string
     *
     * @ORM\Column(name="ProofOfAddress", type="string", length=255,nullable=true)
     */
    private $proofOfAddress;
    
    /**
     * @ORM\OneToOne(targetEntity="P4M\UserBundle\Entity\User", inversedBy="mangoUserNatural",cascade="persist")
     */
    private $user;
    
    /**
     *
     * @var datetime
     * @ORM\Column(name="creationDate",type="datetime")
     */
    private $creationDate;
    
    /**
     *
     * @var integer
     * @ORM\Column(name="mangoId",type="integer")
     */
    private $mangoId;
    /**
     *
     * @var string
     * @ORM\Column(name="tag",type="string",nullable=true)
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
     * Set firstName
     *
     * @param string $firstName
     * @return MangoUserNatural
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
     * @return MangoUserNatural
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

    

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return MangoUserNatural
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return MangoUserNatural
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    
        return $this;
    }

    /**
     * Get occupation
     *
     * @return string 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set incomeRange
     *
     * @param integer $incomeRange
     * @return MangoUserNatural
     */
    public function setIncomeRange($incomeRange)
    {
        $this->incomeRange = $incomeRange;
    
        return $this;
    }

    /**
     * Get incomeRange
     *
     * @return integer 
     */
    public function getIncomeRange()
    {
        return $this->incomeRange;
    }

    /**
     * Set proofOfIdentity
     *
     * @param string $proofOfIdentity
     * @return MangoUserNatural
     */
    public function setProofOfIdentity($proofOfIdentity)
    {
        $this->proofOfIdentity = $proofOfIdentity;
    
        return $this;
    }

    /**
     * Get proofOfIdentity
     *
     * @return string 
     */
    public function getProofOfIdentity()
    {
        return $this->proofOfIdentity;
    }

    /**
     * Set proofOfAddress
     *
     * @param string $proofOfAddress
     * @return MangoUserNatural
     */
    public function setProofOfAddress($proofOfAddress)
    {
        $this->proofOfAddress = $proofOfAddress;
    
        return $this;
    }

    /**
     * Get proofOfAddress
     *
     * @return string 
     */
    public function getProofOfAddress()
    {
        return $this->proofOfAddress;
    }
    
    
    
    public function hydrate(\P4M\UserBundle\Entity\User $user)
    {
        $this->user = $user;
        $user->setMangoUserNatural($this);
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getSurName();
        $this->address = $user->getAddress();
        $this->birthday = $user->getBirthDate()->getTimestamp();
        $this->nationality = $user->getCountry()->getAlpha2();
        $this->countryOfResidence = $user->getCountry()->getAlpha2();
        
    }

    /**
     * Set address
     *
     * @param string $address
     * @return MangoUserNatural
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
     * Set user
     *
     * @param \P4M\UserBundle\Entity\User $user
     * @return MangoUserNatural
     */
    public function setUser(\P4M\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        $this->hydrate($user);
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return MangoUserNatural
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set mangoId
     *
     * @param integer $mangoId
     * @return MangoUserNatural
     */
    public function setMangoId($mangoId)
    {
        $this->mangoId = $mangoId;
    
        return $this;
    }

    /**
     * Get mangoId
     *
     * @return integer 
     */
    public function getMangoId()
    {
        return $this->mangoId;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return MangoUserNatural
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
    
    public function getNationality()
    {
        return $this->nationality;
    }
}
