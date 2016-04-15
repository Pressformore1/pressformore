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
            $file_root = $this->container->get('kernel')->getRootDir() . '/../web/api/list.json';
            $entity_id = $entity->getId();
            $list = file_get_contents($file_root);
            $data = json_decode($list, true);
            $data[$entity_id]['sourceUrl'] = $entity->getSourceUrl();
            $router= $this->container->get('router');
            $data[$entity_id]['url'] = $router->generate('p4m_core_post', $entity->getSlug());
            $new_list = json_encode($data);
            file_put_contents($file_root, $new_list);
        }
    }

    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}