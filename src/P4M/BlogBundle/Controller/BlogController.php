<?php

namespace P4M\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction($page)
    {
        $user = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $repositoryManager = $this->container->get('fos_elastica.manager.orm');
        $repository = $repositoryManager->getRepository('P4MCoreBundle:Post');
        $nombrePostsParPage = $this->container->getParameter('nombre_post_par_page');
        $results = $repository->findPressablePosts([],$page,$nombrePostsParPage);
        
        $nombrePages = ceil($results['count']/$nombrePostsParPage);
        
        $postTypesRepo = $em->getRepository("P4MCoreBundle:PostType");
        $postTypes = $postTypesRepo->findAll();
        
        
        $categoriesId= array();
        $categoriesIdCheck= array();
        foreach ($results['entities'] as $post)
        {
            foreach ($post->getCategories() as $category)
            {
                if (!in_array($category->getId(), $categoriesIdCheck,true))
                {
                    $categoriesId[] = array('id'=>$category->getId());
                    $categoriesIdCheck[] = $category->getId();
                }
            }
                
        }
        
        if (count($categoriesIdCheck))
        {
            $categoriesRepo = $em->getRepository('P4MCoreBundle:Category');
            $categories = $categoriesRepo->findById($categoriesIdCheck);
        }
        else
        {
            $categories = [];
        }
        

        $params =
        [
            'posts'=> $results['entities'] ,
            'categories'=>$categories,
            'postTypes'=>$postTypes,
            'user'=>$user
        ];
        return $this->render('P4MBlogBundle::home.html.twig',$params);
    }
}
