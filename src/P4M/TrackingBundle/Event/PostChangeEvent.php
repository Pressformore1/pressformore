<?php
/**
 * Description of PostChangeEvent
 *
 * @author Jona
 */


namespace P4M\TrackingBundle\Event;

use \P4M\CoreBundle\Entity\Post;
use \P4M\UserBundle\Entity\User;
class PostChangeEvent {
    
    protected $post;
    protected $user;
    protected $date;
    protected $archive;
    
    
    public function __construct(Post $post, User $user)
    {
      $this->post  = $post;
      $this->user     = $user;
    }
    
    
    public function getPost()
    {
        return $this->post;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function getArchive()
    {
        return $this->archive;
    }
}
