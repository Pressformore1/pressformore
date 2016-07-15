<?php


namespace P4M\APIBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use P4M\APIBundle\Services\UpdateList;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\TempAuthor;
use P4M\CoreBundle\Entity\WantPressform;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListUpdaterSuscriber implements EventSubscriber
{
    private $updater;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'preRemove',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
        $this->update($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
        $this->update($args);
    }

    public function preRemove(LifecycleEventArgs $args){
        $this->index($args);
        $entity = $args->getEntity();

        if ($entity instanceof Post)
        {
            $this->updater->deletePost($entity->getId());
            $this->updater->save();
        }

    }

    public function index(LifecycleEventArgs $args)
    {


    }

    public function setUpdater(UpdateList $updater)
    {
        $this->updater = $updater;
    }

    public function update($args){
        $entity = $args->getEntity();

        if ($entity instanceof WantPressform) {
            $post = $entity->getPost();
            $this->updater->updateList($post);
        }
        elseif ($entity instanceof TempAuthor) {
            $post = $entity->getPost();
            $this->updater->updateList($post);
        }
        elseif ($entity instanceof Post) {
            $this->updater->updateList($entity);
        }
        $this->updater->save();
    }
}