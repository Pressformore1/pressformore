<?php

namespace P4M\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WallAutoFollow
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WallAutoFollow
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    
    /**
     *
     * @ORM\OneToOne(targetEntity="P4M\CoreBundle\Entity\Wall")
     */
    private $wall;


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
     * Set name
     *
     * @param string $name
     * @return WallAutoFollow
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set wall
     *
     * @param \P4M\CoreBundle\Entity\Wall $wall
     * @return WallAutoFollow
     */
    public function setWall(\P4M\CoreBundle\Entity\Wall $wall = null)
    {
        $this->wall = $wall;
    
        return $this;
    }

    /**
     * Get wall
     *
     * @return \P4M\CoreBundle\Entity\Wall 
     */
    public function getWall()
    {
        return $this->wall;
    }
}