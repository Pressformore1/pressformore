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
            $file_root_full = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
            $file_root_tmp = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
            $post = $entity->getPost();
            $entity_id = $post->getId();
            $list_full = file_get_contents($file_root_full);
            $data_full = json_decode($list_full, true);
            $data_full[$entity_id]['sourceUrl'] = $post->getSourceUrl();
            $data_full[$entity_id]['slug'] = $post->getSlug();
            $data_full[$entity_id]['author']['email'] = $entity->getEmail();
            $data_full[$entity_id]['author']['twitter'] = $entity->getTwitter();
            $new_list_full = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_full);

            $list_tmp = file_get_contents($file_root_full);
            $data_tmp = json_decode($list_tmp, true);
            $data_tmp[$entity_id]['sourceUrl'] = $post->getSourceUrl();
            $data_tmp[$entity_id]['slug'] = $post->getSlug();
            $data_tmp[$entity_id]['author']['email'] = $entity->getEmail();
            $data_tmp[$entity_id]['author']['twitter'] = $entity->getTwitter();
            $new_list_tmp = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_tmp);

        }
    }
    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}