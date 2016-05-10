<?php


namespace P4M\APIBundle\Listener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use P4M\CoreBundle\Entity\TempAuthor;
use P4M\CoreBundle\Entity\WantPressform;
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
            $list_tmp = file_get_contents($file_root_full);
            $data_tmp = json_decode($list_tmp, true);
            $list_full = file_get_contents($file_root_full);
            $data_full = json_decode($list_full, true);
            if(array_key_exists($entity_id, $data_tmp)){
                unset($data_tmp[$entity_id]);
            }
            if (array_key_exists($entity_id, $data_full)){
                unset($data_full[$entity_id]);
            }
            $data_full[$entity_id]['sourceUrl'] = $post->getSourceUrl();
            $data_full[$entity_id]['slug'] = $post->getSlug();
            $data_full[$entity_id]['author']['email'] = $entity->getEmail();
            $data_full[$entity_id]['author']['twitter'] = $entity->getTwitter();
            $new_list_full = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_full);

            $data_tmp[$entity_id]['sourceUrl'] = $post->getSourceUrl();
            $data_tmp[$entity_id]['slug'] = $post->getSlug();
            $data_tmp[$entity_id]['author']['email'] = $entity->getEmail();
            $data_tmp[$entity_id]['author']['twitter'] = $entity->getTwitter();
            $new_list_tmp = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_tmp);

        }
        elseif ($entity instanceof WantPressform){
            $file_root_full = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlistfull.json';
            $file_root_tmp = $this->container->get('kernel')->getRootDir() . '/../web/api/list/rewardlisttmp.json';

            $post = $entity->getPost();
            $entity_id = $post->getId();

            $d['sourceUrl'] = $post->getSourceUrl();
            $d['slug'] = $post->getSlug();
            $d['status'] = 'NO VALIDATE';
            $d['wantpress'][$entity->getUser()->getId()]['email'] = $entity->getEmail();
            $d['wantpress'][$entity->getUser()->getId()]['twitter'] = $entity->getTwitter();

            $data_full[$entity_id] = $d;
            $new_list_full = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_full);

            $data_tmp[$entity_id] = $d;
            $new_list_tmp = json_encode($data_full);
            file_put_contents($file_root_full, $new_list_tmp);
        }
    }
    public function setContainer(ContainerInterface $container){
        $this->container = $container;
    }
}