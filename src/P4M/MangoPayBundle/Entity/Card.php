<?php

namespace P4M\MangoPayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Card
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Card
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
     * @ORM\Column(name="data", type="string", length=255)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="accessKeyRef", type="string", length=255)
     */
    private $accessKeyRef;

    /**
     * @var string
     *
     * @ORM\Column(name="returnURL", type="string", length=255)
     */
    private $returnURL;

    /**
     * @var string
     *
     * @ORM\Column(name="cardNumber", type="string", length=255)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="CardExpirationDate", type="string", length=255)
     */
    private $cardExpirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="cardCvx", type="string", length=255)
     */
    private $cardCvx;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="actionURL", type="string", length=255)
     */
    private $actionURL;


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
     * Set data
     *
     * @param string $data
     * @return Card
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set accessKeyRef
     *
     * @param string $accessKeyRef
     * @return Card
     */
    public function setAccessKeyRef($accessKeyRef)
    {
        $this->accessKeyRef = $accessKeyRef;
    
        return $this;
    }

    /**
     * Get accessKeyRef
     *
     * @return string 
     */
    public function getAccessKeyRef()
    {
        return $this->accessKeyRef;
    }

    /**
     * Set returnURL
     *
     * @param string $returnURL
     * @return Card
     */
    public function setReturnURL($returnURL)
    {
        $this->returnURL = $returnURL;
    
        return $this;
    }

    /**
     * Get returnURL
     *
     * @return string 
     */
    public function getReturnURL()
    {
        return $this->returnURL;
    }

    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     * @return Card
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    
        return $this;
    }

    /**
     * Get cardNumber
     *
     * @return string 
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set cardExpirationDate
     *
     * @param string $cardExpirationDate
     * @return Card
     */
    public function setCardExpirationDate($cardExpirationDate)
    {
        $this->cardExpirationDate = $cardExpirationDate;
    
        return $this;
    }

    /**
     * Get cardExpirationDate
     *
     * @return string 
     */
    public function getCardExpirationDate()
    {
        return $this->cardExpirationDate;
    }

    /**
     * Set cardCvx
     *
     * @param string $cardCvx
     * @return Card
     */
    public function setCardCvx($cardCvx)
    {
        $this->cardCvx = $cardCvx;
    
        return $this;
    }

    /**
     * Get cardCvx
     *
     * @return string 
     */
    public function getCardCvx()
    {
        return $this->cardCvx;
    }

    /**
     * Set actionURL
     *
     * @param string $actionURL
     * @return Card
     */
    public function setActionURL($actionURL)
    {
        $this->actionURL = $actionURL;
    
        return $this;
    }

    /**
     * Get actionURL
     *
     * @return string 
     */
    public function getActionURL()
    {
        return $this->actionURL;
    }
}
