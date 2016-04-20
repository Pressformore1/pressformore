<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use P4M\BackofficeBundle\Entity\ReadPostLater;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\Pressform;
use P4M\CoreBundle\Entity\Vote;
use P4M\CoreBundle\Entity\WantPressform;
use P4M\CoreBundle\Form\PostType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\Post("/post")
     * @ApiDoc(
     *     resource="Post",
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
     *     resource="Post",
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

//    /**
//     * @param Request $request
//     * @ApiDoc(
//     *     resource=true,
//     *     description="Edit a post"
//     * )
//     */
//    public function putPostAction(Request $request){
//
//    }
//
//    /**
//     * @param Request $request
//     * @ApiDoc(
//     *     resource=true,
//     *     description="Delete a post"
//     * )
//     */
//    public function deletePostAction(Request $request){
//
//    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @ApiDoc(
     *     resource="Post",
     *     description="Preview a post before add",
     *     requirements={
     *          {"name"="url", "dataType"="string", "required"=true, "description"="an url for analyze"},
     *
     *     }
     * )
     * @Rest\View()
     */
    public function getPostPreviewAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mCore.post_utils');
        if(!empty($url = $request->query->get('url'))){
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $post = $postRepo->findOneBySourceUrl($url);
            if(null !== $post){
                $this->response['status_codes'] = 500;
                $this->response['message']= 'Cette article a déjà été partagé';
                return $this->response;
            }
            $metas = $userUtils->grabMetas($url);
            if(!empty($metas)) {$this->response['metas'] = $metas;}
            else{
                $this->response['status_codes'] = 500;
                $this->response['message']= 'Il n\'y pas d\'information a extraire';
            }
        }
        return $this->response;
    }

    /**
     * @Rest\Post("/post/readlater")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Add a post in read later",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *     }
     * )
     */
    public function postPostReadlaterAction(Request $request){

        $user =$this->getUser();
        $em = $this->getDoctrine()->getManager();
        $post_id = $request->request->get('id');
        $check_if_exists = $em->getRepository('P4MBackofficeBundle:ReadPostLater')->findBy(['id' => $post_id, 'user' => $user]);

        if($check_if_exists === null){
            $post = $em->getRepository('P4MCoreBundle:Post')->find($post_id);
            if($post !== null){
                $readPostLater = new ReadPostLater();
                $readPostLater->setUser($user);
                $readPostLater->setPost($post);
                $post->addReadLater($readPostLater);
                $em->persist($post);
                $em->persist($readPostLater);
                $em->flush();
                $this->response['status_codes'] = 200;
                $this->response['message'] = 'Post as been added for read later';
                return $this->response;
            }
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'Post Doesn\'t exist';
            return $this->response;
        }
        $this->response['status_codes'] = 501;
        $this->response['message'] = 'This post is already on read later';
        return $this->response;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Remove a post in read later",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *     },
     *     statusCodes={
     *              500="This post is not on read later",
     *     }
     * )
     */
    public function deletePostReadlaterAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $post_id = $request->request->get('id');
        $readPostLater = $em->getRepository('P4MBackofficeBundle:ReadPostLater')->findOneBy(['id' => $post_id, 'user' => $user]);
        if($readPostLater !== null){
            $post = $em->getRepository('P4MCoreBundle:Post')->find($post_id);
            $post->removeReadLater($readPostLater);
            $em->persist($post);
            $em->remove($readPostLater);
            $em->flush();
            $this->response['status_codes'] = 200;
            $this->response['message'] = 'This post is not more on read later';
            return $this->response;
        }
        $this->response['status_codes'] = 500;
        $this->response['message'] = 'This post is not on read later';
        return $this->response;
    }

    /**
     * @Rest\Post("post/press")
     * @param Request $request
     * @return Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Press a post",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *     },
     *     statusCodes={
     *              500="post already press",
     *     }
     * )
     */
    public function postPostPressAction(Request $request){

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('P4MCoreBundle:Post')->find($request->request->get('id'));

        $pressForm = $em->getRepository('P4MCoreBundle:Pressform')->findOneBy(['post'=>$post,'sender'=>$user,'payed'=>false]);

        if( null !== $pressForm){
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'post already press';
            return $this->response;
        }

        $unpressForm = $em->getRepository('P4MCoreBundle:Unpressform')->findOneBy(['post'=>$post,'user'=>$user]);
        if(null !== $unpressForm)
            $em->remove($unpressForm);

        $pressForm = new Pressform();
        $pressForm->setSender($user);
        $pressForm->setPost($post);
        $em->persist($pressForm);
        $em->flush();
        $this->response['status_codes'] = 200;
        $this->response['message'] = 'post has been pressed';
        return $this->response;
    }

    /**
     * @Rest\Put("post/press")
     * @param Request $request
     * @return Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="mean us why you unpress this content",
     *     requirements={
     *          {"name"="type", "dataType"="integer", "required"=true, "description"="type"},
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *     },
     *     statusCodes={
     *              500="This post is not unpressed",
     *              501="The type is not valid",
     *     }
     * )
     */
    public function putPostPressAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $post = $em->getRepository('P4MCoreBundle:Post')->find($data['id']);
        $unpressform = $em->getRepository('P4MCoreBundle:Unpressform')->findOneBy(['post'=>$post,'user'=>$user]);
        $type = $em->getRepository('P4MCoreBundle:UnpressformType')->find($data['type']);
        if($unpressform === null){
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'This post is not unpressed';
            return $this->response;
        }
        elseif($type === null){
            $this->response['status_codes'] = 501;
            $this->response['message'] = 'The type is not valid';
            return $this->response;
        }
        $unpressform->setType($type);
        $em->persist($unpressform);
        $em->flush();
        $this->response['status_codes'] = 200;
        $this->response['message'] = 'reason of unpress has been added';
        return $this->response;

    }

    /**
     * @Rest\Delete("post/press")
     * @param Request $request
     * @return Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Unpress a post",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *     },
     *     statusCodes={
     *              500="This post is not pressed",
     *     }
     * )
     */
    public function deletePostPressAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('P4MCoreBundle:Post')->find($request->request->get('id'));
        $pressForm = $em->getRepository('P4MCoreBundle:Pressform')->findOneBy(['post'=>$post,'sender'=>$user,'payed'=>false]);

        if(null == $pressForm){
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'This post is not pressed';
            return $this->response;
        }
        $unpress = new \P4M\CoreBundle\Entity\Unpressform();
        $unpress->setPost($post);
        $unpress->setUser($user);
        $em->persist($unpress);
        $em->remove($pressForm);
        $em->flush();

        $this->response['status_codes'] = 200;
        $this->response['message'] = 'This post is not pressed anymore';
        return $this->response;
    }

    /**
     * @Rest\Post("post/vote")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Vote for a post",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *          {"name"="score", "dataType"="integer", "required"=true, "description"="id post"},
     *     }
     * )
     */
    public function postPostVoteAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $id = $request->request->get('id');
        $score = $request->request->get('score');
        $post = $em->getRepository('P4MCoreBundle:Post')->find($id);
        $userVote = $em->getRepository('P4MCoreBundle:Vote')->findOneBy(array('post'=>$id,'user'=>$user->getId()));
        if (null === $userVote)
        {
            $userVote = new Vote();
            $userVote->setUser($user);
            $userVote->setPost($post);
        }
        $userVote->setScore($score);
        $em->persist($userVote);
        $em->flush();
        $postitiveVotesNumber = $em->getRepository('P4MCoreBundle:Post')->countPositive($post);
        $negativeVotesNumber = $em->getRepository('P4MCoreBundle:Post')->countNegative($post);
        $this->response['status_codes'] = 200;
        $this->response['message'] = 'Vote has been added';
        $this->response['positiveVotesNumber'] = $postitiveVotesNumber;
        $this->response['negativeVotesNumber'] = $negativeVotesNumber;
        $this->response['scoreVoted'] = $userVote->getScore();
        return $this->response;
    }

    /**
     * @param Request $request
     * @Rest\Post("post/infoauthor")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Rest\View()
     * @ApiDoc(
     *     resource="Post",
     *     description="Help us for find an author",
     *     requirements={
     *          {"name"="id", "dataType"="integer", "required"=true, "description"="id post"},
     *          {"name"="email", "dataType"="email", "required"=false, "description"="author email"},
     *          {"name"="tweeter", "dataType"="tweeter_account", "required"=false, "description"="author twetter account"},
     *     },
     *     statusCodes={
     *              500="You have already post information about this post/author",
     *     }
     * )
     */
    public function postInfoAuthorAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $id = $request->request->get('id');
        $email = $request->request->get('email');
        $twitter = $request->request->get('twitter');
        $post = $em->getRepository('P4MCoreBundle:Post')->find($id);
        $wantPressForm = $em->getRepository('P4MCoreBundle:WantPressform')->findOneBy(['user'=>$user,'post'=>$post]);
        if(null === $wantPressForm)
        {
            $wantPressForm = new WantPressform();
            $wantPressForm->setPost($post);
            $wantPressForm->setUser($user);
            $wantPressForm->setEmail($email);
            $wantPressForm->setTwitter($twitter);

            $em->persist($wantPressForm);
            $em->flush();
            $this->response['status_codes'] = 200;
        }
        else{
            $this->response['status_codes'] = 500;
        }
        return $this->response;
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
