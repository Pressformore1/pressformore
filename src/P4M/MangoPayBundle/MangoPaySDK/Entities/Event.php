<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Entities;

/**
 * Event entity
 */
class Event extends Dto {
    
    /**
     * Ressource ID
     * @var string 
     */
    public $ResourceId;
    
    /**
     * Event type
     * @var \P4M\MangoPayBundle\MangoPaySDK\EventType 
     */
    public $EventType;
        
    /**
     * Date of event
     * @var Date 
     */
    public $Date;
}