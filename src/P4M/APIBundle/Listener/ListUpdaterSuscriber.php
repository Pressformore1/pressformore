<?php


namespace P4M\APIBundle\Listener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use P4M\CoreBundle\Entity\Post;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ListUpdaterSuscriber implements EventSubscriber
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
        if ($entity instanceof Post) {
            if($entity->getAuthor() == null)
                return ;
            $file_root = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlist.json';
            $file_root_full = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
            $entity_id = $entity->getId();
            $list = file_get_contents($file_root);
            $data = json_decode($list, true);
            $data[$entity_id]['sourceUrl'] = $entity->getSourceUrl();
            $data[$entity_id]['slug'] = $entity->getSlug();
            $author = $entity->getAuthor();
            $data[$entity_id]['author']['username'] = $author->getUsername();
            $data[$entity_id]['author']['producerKey'] = $author->getProducerKey();
            $data[$entity_id]['author']['picture'] = $author->getPicture()->getFile();
            if(!empty($data[$entity_id]['author']['twitter']))
                unset($data[$entity_id]['author']['twitter']);
            if(!empty($data[$entity_id]['author']['email']))
                unset($data[$entity_id]['author']['email']);
            $new_list = json_encode($data);
            file_put_contents($file_root, $new_list);

            $list_full = file_get_contents($file_root_full);
            $data_full = json_decode($list_full, true);
            $data_full[$entity_id]['sourceUrl'] = $entity->getSourceUrl();
            $data_full[$entity_id]['slug'] = $entity->getSlug();
            $author = $entity->getAuthor();
            $data_full[$entity_id]['author']['username'] = $author->getUsername();
            $data_full[$entity_id]['author']['producerKey'] = $author->getProducerKey();
            $data_full[$entity_id]['author']['picture'] = $author->getPicture()->getFile();
            if(!empty($data_full[$entity_id]['author']['twitter']))
                unset($data_full[$entity_id]['author']['twitter']);
            if(!empty($data_full[$entity_id]['author']['email']))
                unset($data_full[$entity_id]['author']['email']);
            $new_list_full = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_full);

        }
    }
    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}