<?php


namespace P4M\APIBundle\Services;


use Symfony\Component\HttpKernel\Kernel;

class BlackListManager
{
    private $file_root;
    private $blacklist;

    public function __construct(Kernel $kernel)
    {
        $this->file_root = $kernel->getRootDir() . '/../web/api/list/blacklist.json';
        $json = file_get_contents($this->file_root);
        $this->blacklist = json_decode($json, true);
        if(!is_array($this->blacklist))
            $this->blacklist = array();
    }

    /**
     * Add a site in blacklist
     * @param $site
     * @return $this
     */
    public function addSite($site){
        if(array_search($site, $this->blacklist) === false)
            $this->blacklist[] = $site;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * Save the blacklist
     */
    public function save(){
        $json = json_encode($this->blacklist);
        file_put_contents($this->file_root, $json);
    }


    public function removeSite($site){
        $key = array_search($site, $this->blacklist);
        if( $key !== false)
            unset($this->blacklist[$key]);
    }
}