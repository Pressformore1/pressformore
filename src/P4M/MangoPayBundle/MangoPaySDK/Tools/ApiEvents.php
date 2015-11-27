<?php
namespace P4M\MangoPayBundle\MangoPaySDK\Tools;

/**
 * Class to management MangoPay API for cards
 */
class ApiEvents extends ApiBase {
    
    /**
     * Get events
     * @param \P4M\MangoPayBundle\MangoPaySDK\Pagination $pagination Pagination object
     * @param \P4M\MangoPayBundle\MangoPaySDK\FilterEvents $filter Object to filter data
     * @return \P4M\MangoPayBundle\MangoPaySDK\Event[] Events list
     */
    public function GetAll(& $pagination = null, $filter = null) {
        return $this->GetList('events_all', $pagination, '\P4M\MangoPayBundle\MangoPaySDK\Event', null, $filter);
    }
}
