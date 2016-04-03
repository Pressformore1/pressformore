<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Form\PostType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class PostController extends FOSRestController
{
    private $response = array();

    public function __construct()
    {
        $this->response = [
            'message' => '',
            'status_codes' => ''
        ];
    }

    /**
     * @Rest\Post("/post")
     * @ApiDoc(
     *     resource=True,
     *     description="add a post",
     *     requirements={
     *          {"name"="title", "dataType"="string", "required"=true, "description"="Title"},
     *          {"name"="content", "dataType"="text", "required"=true, "description"="Content"},
     *          {"name"="picture", "dataType"="url", "required"=true, "description"="Picture"},
     *          {"name"="pictureList", "dataType"="array()", "required"=true, "description"="List of picture"},
     *          {"name"="iframeAllowed", "dataType"="boolean", "required"=true, "description"=""},
     *          {"name"="categories", "dataType"="array()", "required"=true, "description"="Categories"},
     *          {"name"="tags", "dataType"="string('tags1, tags2, tags3, ...')", "required"=true, "description"=""},
     *          {"name"="sourceUrl", "dataType"="url", "required"=true, "description"=""},
     *          {"name"="type", "dataType"="integer", "required"=true, "description"=""},
     *          {"name"="lang", "dataType"="integer", "required"=true, "description"=""},
     *     }
     * )
     */
    public function postPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $post = new Post();
        $data = $request->request->all();

        if($this->checkKey($data)){
            $search = $em->getRepository('P4MCoreBundle:Post')->findOneBySourceUrl($data['sourceUrl']);
            if(null === $search){
                $form = $this->get('form.factory')->create(new PostType($data['pictureList']), $post, ['method' => 'PUT' ]);
                $form->submit($data);
                if($form->isValid()){
                    $post->setUser($this->getUser());
                    $postUtils = $this->get('p4mCore.post_utils');

                    $authorKey = $postUtils->getPostAuthorMeta($post->getSourceUrl());

                    if (null !== $authorKey)
                    {
                        $userRepo = $em->getRepository('P4MUserBundle:User');
                        $author = $userRepo->findOneByProducerKey($authorKey);

                        if (null !== $author)
                        {
                            $post->setAuthor($author);
                        }
                    }
                    $em->persist($post);

                    foreach($post->getTags() as $tag)
                    {
                        $tag->addPost($post);
                        $em->persist($tag);
                    }
                    foreach($post->getCategories() as $cat)
                    {
                        $cat->addPost($post);
                        $em->persist($cat);
                    }
                    $em->flush();
                    // $this->response['post'] = $post;
                    $this->response['status_codes'] = 200;
                    $this->response['message'] = 'post has been added';
                }else{
                    $this->response['error'] = 'no Valid';
                    $this->response['data'] = $request->request->all();
                    $this->response['errorMessage'] = $form->getErrors();
                }
            }
            else{
                $this->response['message'] = 'post already exists';
            }
        }
        else{
            $this->response['message'] = 'des arguments son manquant';
            $this->response['data'] = $request->request->all();
        }

        $view = $this->view($this->response);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @param Request $request
     * @ApiDoc(
     *     resource=false,
     *     description="Get a post"
     * )
     * @Rest\View()
     */
    public function getPostAction(Request $request){
        if($slug = $request->query->get('slug')){
            $post = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Post')->findOneBySlug($slug);
            $this->response['status_codes'] = 200;
            $this->response['post'] = $post->toArray();
        }else{
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'Need a slug for find a post';
        }

        return $this->response;
    }

    /**
     * @param Request $request
     * @ApiDoc(
     *     resource=true,
     *     description="Edit a post"
     * )
     */
    public function putPostAction(Request $request){

    }

    /**
     * @param Request $request
     * @ApiDoc(
     *     resource=true,
     *     description="Delete a post"
     * )
     */
    public function deletePostAction(Request $request){

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @ApiDoc(
     *     resource=True,
     *     description="Preview a post before add",
     *     requirements={
     *          {"name"="url", "dataType"="string", "required"=true, "description"="an url for analyze"},
     *
     *     }
     * )
     */
    public function getPostPreviewAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mCore.post_utils');

        if(!empty($url = $request->query->get('url'))){
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $langueRepo = $em->getRepository('P4MCoreBundle:Lang');

            $post = $postRepo->findOneBySourceUrl($url);

            if(null === $post){
                $metas = $userUtils->grabMetas($url);
            }
            $this->response['metas'] = $metas;
        }
        $view = $this->view($this->response);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     resource=True,
     *     description="test the form",
     *     input="PostType"
     * )
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putTestFormAction(Request $request){
        $post = new Post();
        $data = [
            'title' => '"La FINANCIARISATION détruit l\'emploi, l\'économie et la démocratie : il faut vite réagir et AGIR ! - AgoraVox le média citoyen"',
            'content' => "\"Mon ennemi c'est la finance\" clamait le prétendant socialo-libéral au poste suprême. Cet ennemi coule des jours heureux en (...)",
            'picture' => "http://i.agoravox.fr/local/cache-vignettes/L318xH90/siteon0-f4356.png",
            'pictureList' => ["http://i.agoravox.fr/local/cache-vignettes/L318xH90/siteon0-f4356.png"],
            'lang' => '27',
            'iframeAllowed' => 1,
            'sourceUrl' => "http://www.agoravox.fr/tribune-libre/article/la-financiarisation-detruit-l-178325",
            'type' => '1',
            'embed' => ""
        ];
        $data['tags'] = 'israel';


        if($request->getMethod() === 'PUT'){
            $data = array_merge($data, $request->request->all());
            if($this->checkKey($data)){
                $form = $this->get('form.factory')->create(new PostType($data['pictureList']), $post, ['method' => 'PUT' ]);
                $form->submit($data);
                if($form->isValid()){
                    $this->response['error'] = 'valid';
                    $post->setUser($this->getUser());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($post);
                    $em->flush();
                    $this->response['post'] = $post;
                }else{
                    $this->response['error'] = 'no Valid';
                    $this->response['fkd'] = $request->request->all();
                    $this->response['errorMessage'] = $form->getErrors();
                    $this->response['data'] = $form->getName();
                }
            }
            else{
                $this->response['status_codes'] = '500';
                $this->response['message'] = 'Des données son manquante';
            }
        }
        $view = $this->view($this->response);
        return $this->handleView($view);
    }

    private function checkKey(array $data){
        foreach($data as $key => $value){
            if(!array_key_exists('title', $data))
                return false;
            if(!array_key_exists('content', $data))
                return false;
            if(!array_key_exists('tags', $data))
                return false;
            if(!array_key_exists('type', $data))
                return false;
            if(!array_key_exists('sourceUrl', $data))
                return false;
            if(!array_key_exists('lang', $data))
                return false;
            if(!array_key_exists('picture', $data))
                return false;
            if(!array_key_exists('pictureList', $data))
                return false;
            if(!array_key_exists('iframeAllowed', $data))
                return false;
            if(!array_key_exists('categories', $data))
                return false;
            return true;
        }
    }
}
