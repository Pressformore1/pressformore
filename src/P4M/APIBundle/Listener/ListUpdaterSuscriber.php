<?php


namespace P4M\APIBundle\Listener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use P4M\CoreBundle\Entity\Post;

use Doctrine\Common\EventSubscriber;
use P4M\CoreBundle\Entity\Pressform;
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
        //met a jour la list si un post est mise a jour
        if ($entity instanceof Post) {
            if($entity->getAuthor() == null)
                return ;
            $file_root = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlist.json';
            $file_root_full = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
            $file_root_tmp = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';
            $list_tmp = file_get_contents($file_root_tmp);
            $list_full = file_get_contents($file_root_full);
            $list = file_get_contents($file_root);
            $data_tmp = json_decode($list_tmp, true);
            $data_full = json_decode($list_full, true);
            $data = json_decode($list, true);
            $entity_id = $entity->getId();
            $author = $entity->getAuthor();
            $d['sourceUrl'] = $entity->getSourceUrl();
            $d['status'] = 'COMPLETE';
            $d['slug'] = $entity->getSlug();
            $d['author']['username'] = $author->getUsername();
            $d['author']['producerKey'] = $author->getProducerKey();
            $d['author']['picture'] = $author->getPicture()->getFile();
            foreach ($entity->getPressforms() as $pressform){
                   $sender = $pressform->getSender();
                   $d['pressform'][$sender->getUsername()]['picture'] = $sender->getPicture()->getWebPath();
            }
            $data[$entity_id] = $d;
            $data_full[$entity_id] = $d;

            $new_list = json_encode($data);
            file_put_contents($file_root, $new_list);

            $new_list_full = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_full);

            if(array_key_exists($entity_id, $data_tmp))
                unset($data_tmp[$entity_id]);

            $new_list_tmp = json_encode($data_tmp);
            file_put_contents($file_root_tmp, $new_list_tmp);
        }
    }
    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}