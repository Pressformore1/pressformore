<?php


namespace P4M\APIBundle\Listener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use P4M\CoreBundle\Entity\TempAuthor;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ListTempAuthorUpdater implements EventSubscriber
{
    private $container;
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
        );
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }
    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof TempAuthor) {
            $file_root = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
            $post = $entity->getPost();
            $entity_id = $post->getId();
            $list = file_get_contents($file_root);
            $data = json_decode($list, true);
            $data[$entity_id]['sourceUrl'] = $post->getSourceUrl();
            $data[$entity_id]['slug'] = $post->getSlug();
            $data[$entity_id]['author']['email'] = $entity->getEmail();
            $data[$entity_id]['author']['twitter'] = $entity->getTwitter();
            $new_list = json_encode($data);
            file_put_contents($file_root, $new_list);
        }
    }
    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}