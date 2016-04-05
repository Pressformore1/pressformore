<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ListController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of category"
     * )
     */
    public function getListCategoryAction()
    {
        $categorys = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:Category')->findAll();

        $response = $this->view($categorys);
        return $this->handleView($response);
    }
    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Type"
     * )
     */
    public function getListTypeAction(){
        $types = $this->getDoctrine()->getManager()->getRepository('P4MCoreBundle:PostType')->findAll();

        $response = $this->view($types);
        return $this->handleView($response);
    }

    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of Country"
     * )
     */
    public function getListCountryAction(){
        $countrys = $this->getDoctrine()->getManager()->getRepository('P4MUserBundle:Country')->findAll();

        $response = $this->view($countrys);
        return $this->handleView($response);
    }
}
