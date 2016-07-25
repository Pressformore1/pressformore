<?php
/**
 * Created by PhpStorm.
 * User: brynnlow
 * Date: 15/07/16
 * Time: 12:33
 */

namespace P4M\APIBundle\Services;


use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\WantPressform;
use P4M\CoreBundle\Entity\Pressform;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

class UpdateList
{
    private $file_root;
    private $file_root_full;
    private $file_root_tmp;

    private $list_tmp;
    private $list_full;
    private $list;

    private $data_full;
    private $data_tmp;
    private $data;

    public function __construct(Kernel $kernel)
    {
        $this->file_root = $kernel->getRootDir() . '/../web/api/list/rewardlist.json';
        $this->file_root_full = $kernel->getRootDir() . '/../web/api/list/rewardlistfull.json';
        $this->file_root_tmp = $kernel->getRootDir() . '/../web/api/list/rewardlisttmp.json';

        $this->list_tmp = file_get_contents($this->file_root_tmp);
        $this->list_full = file_get_contents($this->file_root_full);
        $this->list = file_get_contents($this->file_root);

        $this->data_tmp = json_decode($this->list_tmp, true);
        $this->data_full = json_decode($this->list_full, true);
        $this->data = json_decode($this->list, true);
    }


    /**
     * Update list for a post without save
     * @param Post $post
     */
    public function updateList(Post $post){
        $author = $post->getAuthor();
        $tmp_author = $post->getTempAuthor();

        if($author != null){
            $data['sourceUrl'] = $post->getSourceUrl();
            $data['slug'] = $post->getSlug();
            $data['status'] = 'COMPLETE';
            $data['author']['username'] = $author->getUsername();
            $data['author']['producerKey'] = $author->getProducerKey();
            $data['author']['picture'] = $author->getPicture()->getWebPath();

            foreach ($post->getPressforms() as $pressform){
                $sender = $pressform->getSender();
                $data['pressform'][$sender->getUsername()] = $sender->getPicture()->getWebPath();
            }

            if(array_key_exists($post->getId(), $this->data_tmp))
                unset($this->data_tmp[$post->getId()]);

            $this->data[$post->getId()] = $data;
        }

        elseif ($tmp_author != null){
            $data['sourceUrl'] = $post->getSourceUrl();
            $data['slug'] = $post->getSlug();
            $data['status'] = 'VALIDATE';
            $data['author']['email'] = $tmp_author->getEmail();
            $data['author']['twitter'] = $tmp_author->getTwitter();

            if($post instanceof  Post){

                foreach ($post->getWantPressforms() as $wantPressform){
                    $data['wantpress'][$wantPressform->getUser()->getUsername()] = $wantPressform->getUser()->getPicture()->getWebPath();
                }

            }
            $this->data_tmp[$post->getId()] = $data;

        }

        else{
            $data['sourceUrl'] = $post->getSourceUrl();
            $data['slug'] = $post->getSlug();
            $data['status'] = 'NO VALIDATE';
            $wants = $post->getWantPressforms();

            if ($wants != null){

                foreach ($wants as $want)
                {
                    $data['wantpress'][$want->getUser()->getUsername()]['email'] = $want->getEmail();
                    $data['wantpress'][$want->getUser()->getUsername()]['twitter'] = $want->getTwitter();
                    $data['wantpress'][$want->getUser()->getUsername()]['picture'] = $want->getUser()->getPicture()->getWebPath();
                }

            }
            $this->data_tmp[$post->getId()] = $data;
        }
        $this->data_full[$post->getId()] = $data;
    }

    /**
     * Delete a post from list
     * @param $id
     */
    public function deletePost($id){

        if (array_key_exists($id, $this->data)){
            unset($this->data[$id]);
        }

        if (array_key_exists($id, $this->data_tmp)){
            unset($this->data_tmp[$id]);
        }

        if (array_key_exists($id, $this->data_full)){
            unset($this->data_full[$id]);
        }

    }

    /**
     * Save the list
     */
    public function save(){
        $new_list_full = json_encode($this->data_full);
        file_put_contents($this->file_root_full, $new_list_full);
        $new_list_tmp = json_encode($this->data_tmp);
        file_put_contents($this->file_root_tmp, $new_list_tmp);
        $new_list = json_encode($this->data);
        file_put_contents($this->file_root, $new_list);
    }

}