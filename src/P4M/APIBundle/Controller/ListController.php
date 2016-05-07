<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
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
     * @Rest\Get("list/category")
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of category"
     * )
     * @Rest\View(serializerGroups={"json"})
     * @return Response
     */
    public function getListCategoryAction()
    {
        $categorys = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Category')->findAll();
        return $categorys;
    }

    /**
     * @Rest\Get("list/type")
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Type"
     * )
     * @Rest\View(serializerGroups={"json"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListTypeAction()
    {
        $types = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:PostType')->findAll();
        return $types;
    }

    /**
     * @Rest\Get("list/country")
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Country"
     * )
     * @Rest\View(serializerGroups={"json"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListCountryAction()
    {
        $countrys = $this->getDoctrine()->getManager()->getRepository('P4MUserBundle:Country')->findAll();
        return $countrys;
    }

    /**
     * @Rest\View(serializerGroups={"list"})
     * @return Response
     * @ApiDoc(
     *     resource="List",
     *     description="Get list post of language",
     *  )
     */
    public function getListLangAction()
    {
        $lang = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Lang')->findAll();
        return $lang;
    }

    /**
     * @Rest\Get("list/post")
     * @param Request $request
     * @return Response
     * @Rest\View(serializerGroups={"list"})
     * @ApiDoc(
     *     resource="List",
     *     description="Get list post of wall",
     *     parameters={
     *              {"name"="page", "dataType"="integer", "required"=false, "description"="page of a wall"},
     *              {"name"="nb_by_page", "dataType"="integer", "required"=false, "description"="how many post you want"},
     *     }
     * )
     */
    public function getListPostAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $wall = $em->getRepository('P4MCoreBundle:Wall')->findOneByUser($user);
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
        $page = (!empty($request->request->get('page'))) ? $request->request->get('page') : 1;
        $nb_by_page = (!empty($request->request->get('nb_by_page'))) ? $request->request->get('nb_by_page') : 30;
        if ($wall === null) {
            $searchResult = $repository->findPressablePosts([], $page, $nb_by_page);
        } else {
            $view = new \P4M\TrackingBundle\Entity\WallView();
            $view->setWall($wall);
            $view->setUser($user);
            $em->persist($view);
            $em->flush();
            $bannedPostId = $em->getRepository('P4MBackofficeBundle:BannedPost')->findIdsByUser($user);
            $postData['categories'] = $wall->getIncludedCatsId();
            $postData['tags'] = $wall->getIncludedTagsId();
            $postData['excludedCategories'] = $wall->getExcludedCatsId();
            $postData['excludedTags'] = $wall->getExcludedTagsId();
            $postData['bannedPost'] = $bannedPostId;
            $searchResult = $repository->findCustom(null, $postData, $page, $nb_by_page);
        }
        $posts = $searchResult['entities'];
        foreach ($posts as $key => $value) {
            $read_later = $value->getReadLater();
            foreach ($read_later as $k => $v) {
                if ($v->getUser()->getUsername() !== $user->getUsername()) {
                    $v->getUser()->setUsername('');
                }
            }
        }
        $this->response['status_codes'] = 200;
        $this->response['nbr_post'] = $searchResult['count'];
        $this->response['posts'] = $posts;
        return $this->response;
    }

    /**
     * @ApiDoc(
     *     resource="List",
     *     description="get list donator about an author",
     *     parameters={
     *        {"name"="author", "dataType"="string", "required"=false, "description"="Author"},
     *        {"name"="slug", "dataType"="string", "required"=false, "description"="slug of post"},
     *        {"name"="url", "dataType"="string", "required"=false, "description"="Source url of post"},
     *     }
     * )
     * @param Request $request
     * @return Response
     * @Rest\View(serializerGroups={"json"})
     */
    public function getListDonatorAction(Request $request)
    {
        $author = $request->query->get('author');
        $slug = $request->query->get('slug');
        $url = $request->query->get('url');
        if (!empty($author)) {
            $list = $this->getDoctrine()->getRepository('P4MCoreBundle:Pressform')->findDonatorForAnAuthor($author);
        }
        elseif (!empty($slug)){
            $list = $this->getDoctrine()->getRepository('P4MCoreBundle:Pressform')->findDonatorForAPost($slug);
        }
        elseif(!empty($url)){
            $list = $this->getDoctrine()->getRepository('P4MCoreBundle:Pressform')->findDonationBySourceURL($url);
        }
        else{
            $user = $this->getUser();
            $list = $this->getDoctrine()->getRepository('P4MCoreBundle:Pressform')->findDonationByUser($user);
        }
        if (null == $list) {
            $this->response['status_codes'] = 201;
            $this->response['message'] = 'resultat is empty';
        } else {
            $this->response['status_codes'] = 200;
            $this->response['data'] = $list;
        }

        return $this->response;
    }

    /**
     * @return Response
     * @Rest\View(serializerGroups={"json"})
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of post pressed"
     * )
     */
    public function getListPressedAction()
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Pressform');

        $data['pressedPayed'] = $repo->createQueryBuilder('P')->where('P.payed = true')
            ->andWhere('P.sender = :sender')
            ->setParameter('sender', $user)
            ->getQuery()
            ->getResult();
        $data['pressed'] = $repo->createQueryBuilder('P')->where('P.payed = false')
            ->andWhere('P.sender = :sender')
            ->setParameter('sender', $user)
            ->getQuery()
            ->getResult();
        if (empty($data['pressedPayed']) && empty($data['pressed'])) {
            $this->response['status_codes'] = 501;
            $this->response['message'] = 'you don\'t have press post';
            return $this->response;
        }
        return $data;
    }

}
