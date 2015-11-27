<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CardRegistration
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CardRegistration
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
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;

    /**
     *
     * @var type 
     * @ORM\ManyToOne(targetEntity="P4M\MangoPayBundle\Entity\MangoUserNatural")
     */
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
     * Set currency
     *
     * @param string $currency
     * @return CardRegistration
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return CardRegistration
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

    /**
     * Set mangoUser
     *
     * @param \P4M\MangoPayBundle\Entity\MangoUserNatural $mangoUser
     * @return CardRegistration
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
}