<?php

namespace P4M\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use P4M\CoreBundle\Entity\Category;
use P4M\CoreBundle\Entity\Post;
use P4M\CoreBundle\Entity\PostType;
use P4M\UserBundle\Entity\User;

class SearchController extends Controller
{
    public function searchAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $categoryRepo = $em->getRepository('P4MCoreBundle:Category');
        $categories = $categoryRepo->findAll();
        
        $keyword = $this->getRequest()->query->get('k');
        $where = 'all';
        $where = $this->getRequest()->query->get('where');
        
        $postDataContainer = $this->get('request')->request->all();
        $postData = [];
        if (isset($postDataContainer['params']))
        {
            $postData = json_decode($postDataContainer['params'],true);
            $keyword = $postData['k'];
            unset($postData['k']);
            $where = $postData['where'];
            unset($postData['where']);
        }
        
//        die(print_r($postData));
        
        $websiteIndex = $this->container->getParameter('elastica_website');
        
        
        
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');

//        die($keyword);
        switch ($where)
        {
            case 'all':$finder = $this->container->get('fos_elastica.finder.'.$websiteIndex);
                break;
            case 'user':$repository = $repositoryManager->getRepository('P4MUserBundle:User');
                break;
            case 'post':$repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
                break;
            case 'strew':$repository = $repositoryManager->getRepository('P4MCoreBundle:Wall');
                break;
            default:$finder = $this->container->get('fos_elastica.finder.'.$websiteIndex);
                break;
                
        }
//        $finder->addPostTransformer('toto');
//        dump($finder);
//        dump(get_class($finder));
//        $finder->getTransformer()->addTransformer($this->get('p4m.transformers.elastica.post'));
        
//        die();
//        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        
        if (isset($repository))
        {
            $searchResults = $repository->findCustom($keyword,$postData,1,$nombrePostsParPage);
//            $searchResults = $repository->findCustom($keyword.'*',$postData,1,$nombrePostsParPage);
            $results = $searchResults['entities'];
            $nombrePages = ceil($searchResults['count']/$nombrePostsParPage);
        }
        else
        {
            if (!isset($finder))
            {
                $finder = $this->container->get('fos_elastica.finder'.$websiteIndex);
            }
            
            $results = $finder->find($keyword,999);
            $nombrePages = 1;
        }
       
        
        $languages = array();
        foreach ($results as $result)
        {
            if ($result instanceof Post)
            {
                if (!in_array($result->getLang(), $languages,true))
                {
                    $langages[] = $result->getLang();
                }
            }
            
        }

//        $results = $finder->find($keyword);
        
        
        
        
        
        
        
        
        $postTypesRepo = $em->getRepository("P4MCoreBundle:PostType");
        $postTypes = $postTypesRepo->findAll();
        
        $params = array
            (
                'user'=>$this->getUser(),
                'postPath'=>$this->generateUrl('p4m_core_search'),
                'results'=> $results ,
                'categories'=>$categories,
                'postTypes'=>$postTypes,
                'nombrePages'=>$nombrePages,
                'filters'=>  http_build_query($this->get('request')->request->all()),
                'keyword'=>$keyword,
                'languages'=>$languages,
                
            );
        return $this->render('P4MCoreBundle:Wall:search.html.twig',$params);
        
    }
    
    public function searchAjaxAction($page)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        
        $repositoryManager = $this->container->get('fos_elastica.manager.orm');

        $postDataContainer = $this->get('request')->request->all();
        if (isset($postDataContainer['params']))
        {
            $postData = json_decode($postDataContainer['params'],true);
        }
        
//        die(print_r($postData));
        
        $keyword = $postData['k'];
        unset($postData['k']);
        $where = $postData['where'];
        unset($postData['where']);
        
        $websiteIndex = $this->container->getParameter('elastica_website');
        
//        die($keyword);
        
        switch ($where)
        {
            case 'all':$finder = $this->container->get('fos_elastica.finder.'.$websiteIndex);
                break;
            case 'user':$repository = $repositoryManager->getRepository('P4MUserBundle:User');
                break;
            case 'post':$repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
                break;
            case 'strew':$repository = $repositoryManager->getRepository('P4MCoreBundle:Wall');
                break;
            default:$finder = $this->container->get('fos_elastica.finder.'.$websiteIndex);
                break;
                
        }
        
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        
        if (isset($repository))
        {
            $searchResults = $repository->findCustom($keyword,$postData,1,$nombrePostsParPage);
            $results = $searchResults['entities'];
            $nombrePages = ceil($searchResults['count']/$nombrePostsParPage);
        }
        else
        {
            if (!isset($finder))
            {
                $finder = $this->container->get('fos_elastica.finder.'.$websiteIndex);
            }
            
            $results = $finder->find($keyword,999);
            $nombrePages = 1;
        }
       
        
        $languages = array();
        foreach ($results as $result)
        {
            if ($result instanceof Post)
            {
                if (!in_array($result->getLang(), $languages,true))
                {
                    $langages[] = $result->getLang();
                }
            }
            
        }

//        $results = $finder->find($keyword);
        
        
        
        
        
        
        
        
        $postTypesRepo = $em->getRepository("P4MCoreBundle:PostType");
        $postTypes = $postTypesRepo->findAll();
        
        $params = array
            (
                'user'=>$user,
                'postPath'=>$this->generateUrl('p4m_core_search_ajax'),
                'results'=> $results ,
                'categories'=>array(),
                'postTypes'=>$postTypes,
                'nombrePages'=>$nombrePages,
                'filters'=>  http_build_query($this->get('request')->request->all())
            );
//        return $this->render('P4MCoreBundle:Wall:search.html.twig',$params);
        
        
        
        
        
        
        $response = array
        (
            'status'=>1,
             'action'=>'setWallPost',
            'data'=>array
            (
                'posts'=>$this->renderView('P4MCoreBundle:Wall:post-collection-ajax.html.twig',$params),
                'pagination'=>$this->renderView('P4MCoreBundle:Wall:pagination.html.twig',$params),
                
                
            )
        );
        
        return new Response(json_encode($response));
    }
    
   
}