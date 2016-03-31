<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PostController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource=True,
     *     description="add a post"
     * )
     */
    public function getAddPostAction(){
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mCore.post_utils');
        $request = $this->get('request');

        $post = new Post();
        $form = $this->get('form.factory')->create(new PostType(), $post);
        $form->remove('blogPost')
            ->remove('anchors');
        if(!empty($url = $request->query->get('url'))){
            $this->response['url']= $url;
            $postRepo = $em->getRepository('P4MCoreBundle:Post');
            $langueRepo = $em->getRepository('P4MCoreBundle:Lang');

            $post = $postRepo->findOneBySourceUrl($url);

            if(null === $post){
                $metas = $userUtils->grabMetas($url);
            }
            $this->response['metas'] = $metas;
            $this->response['form'] = $form;

            $view = $this->view($this->response);
            return $this->handleView($view);
        }
        $response = [
            'message' => '',
            'status_codes' => '',
            'form' => $form,
        ];
        $view = $this->view($response);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *     resource=True,
     *     description="Preview a post before add",
     *     requirements={
     *          {"name"="url", "dataType"="string", "required"=true, "description"="an url for analyze"},
     *
     *     }
     * )
     */
    public function getPreviewPostAction(){
        $em = $this->getDoctrine()->getManager();
        $userUtils = $this->get('p4mCore.post_utils');
        $request = $this->get('request');

        if(!empty($url = $request->query->get('url'))){
            $this->response['url']= $url;
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
}
