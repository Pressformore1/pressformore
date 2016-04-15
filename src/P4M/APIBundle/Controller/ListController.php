<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListController extends FOSRestController
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
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of category"
     * )
     * @View(serializerGroups={"json"})
     * @return Response
     */
    public function getListCategoryAction()
    {
        $categorys = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Category')->findAll();
        return $categorys;
    }
    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Type"
     * )
     * @View(serializerGroups={"json"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListTypeAction(){
        $types = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:PostType')->findAll();
        return $types;
    }

    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Country"
     * )
     * @View(serializerGroups={"json"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListCountryAction(){

        $countrys = $this->getDoctrine()->getManager()->getRepository('P4MUserBundle:Country')->findAll();

        return $countrys;
    }

    public function getListUrlAction(){

    }

    /**
     * @Get("list/post")
     * @param Request $request
     * @return Response
     * @View(serializerGroups={"list"})
     * @ApiDoc(
     *     resource="List",
     *     description="Get list post of wall",
     *     parameters={
     *              {"name"="slug", "dataType"="string", "required"=true, "description"="slug of a wall"},
     *              {"name"="page", "dataType"="integer", "required"=false, "description"="page of a wall"},
     *              {"name"="nb_by_page", "dataType"="integer", "required"=false, "description"="how many post you want"},
     *     }
     * )
     */
    public function getListPostInwallAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $slug = $request->query->get('slug');
        $page = (!empty($request->request->get('page'))) ? $request->request->get('page') : 1;
        $nb_by_page = (!empty($request->request->get('nb_by_page'))) ? $request->request->get('nb_by_page') : 30;
        $wall = $em->getRepository('P4MCoreBundle:Wall')->findOneBySlug($slug);
        if($wall === null){
            $this->response['status_codes'] = 500;
            $this->response['message'] = 'this wall don\'t exist';
            return $this->response;
        }
        $view = new \P4M\TrackingBundle\Entity\WallView();
        $view->setWall($wall);
        $view->setUser($user);
        $em->persist($view);
        $em->flush();
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
        $bannedPostId = $em->getRepository('P4MBackofficeBundle:BannedPost')->findIdsByUser($user);
        $postData['categories'] = $wall->getIncludedCatsId();
        $postData['tags'] = $wall->getIncludedTagsId();
        $postData['excludedCategories']=$wall->getExcludedCatsId();
        $postData['excludedTags']=$wall->getExcludedTagsId();
        $postData['bannedPost']=$bannedPostId;
        $searchResult = $repository->findCustom(null,$postData, $page, $nb_by_page);
        $posts = $searchResult['entities'];
        foreach($posts as $key => $value){
            $read_later = $value->getReadLater();
            foreach($read_later as $k => $v){
                if($v->getUser()->getUsername() !== $user->getUsername()){
                    $v->getUser()->setUsername('');
                }
            }
        }
        $this->response['status_codes'] = 200;
        $this->response['nbr_post'] = $searchResult['count'];
        $this->response['posts'] = $posts;
        return $this->response;
    }
}
