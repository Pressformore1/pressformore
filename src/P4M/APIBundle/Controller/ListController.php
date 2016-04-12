<?php

namespace P4M\APIBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ListController extends FOSRestController
{
    /**
     * @ApiDoc(
     *     resource="List",
     *     description="Get list of category"
     * )
     * @View(serializerGroups={"json"})
     * @return \Symfony\Component\HttpFoundation\Response
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
}
