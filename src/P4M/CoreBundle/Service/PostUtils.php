<?php //

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostUtils
 *
 * @author Jona
 */


namespace P4M\CoreBundle\Service;
use P4M\CoreBundle\Entity\Post;
use Symfony\Component\DependencyInjection\Container;


class PostUtils 
{
    
                    
    
//    private $encoding;
//    
//    private $container;
    
    private $analyser;
    
    public function __construct(PostSourceAnalyser $analyser,$container)
    {
        $this->analyser=$analyser;
        $this->container=$container;
    }
//    public function __construct($encoding,Container $container)
//    {
//        $this->encoding=$encoding;
//        $this->container=$container;
//    }
    
            
    public function grabMetas($sourceUrl)
    {
        $postMeta = array();
        
		
        $this->analyser->load($sourceUrl);
            
        $postMeta['content']= $this->analyser->getDescription();
        $postMeta['title'] = $this->analyser->getTitle();
        $postMeta['type'] = $this->analyser->getType();
        $postMeta['tags'] = $this->analyser->getTags();
        $postMeta['embed'] = $this->analyser->getEmbed();
        $postMeta['picture'] = $this->analyser->getPicture();
        $postMeta['sourcePictureList'] = null !== $postMeta['picture'] ? [$postMeta['picture']] : [];
        $postMeta['lang'] = $this->analyser->getLanguage();
        $postMeta['sourceUrl'] = $this->analyser->getCanonical() !== null ? $this->analyser->getCanonical() : $sourceUrl;
        $postMeta['iframeAllowed'] = $this->analyser->isIframeAllowed();
        
        if (!count($postMeta['sourcePictureList']) && null !== $this->analyser->getPictureList() && count($this->analyser->getPictureList()))
        {
            $postMeta['sourcePictureList'] = $this->analyser->getPictureList();
        }
        else if(!count($postMeta['sourcePictureList']) && (null === $this->analyser->getPictureList() || !count($this->analyser->getPictureList())))
        {
            $postMeta['sourcePictureList'][] = $this->container->get('templating.helper.assets')->getUrl('images/design/default-pic.jpg');
        }

       

        
        
        return $postMeta;
    }
    
        
    public function getPostAuthorMeta($sourceUrl)
    {
        $this->analyser->load($sourceUrl);
        return $this->analyser->getKey();

        }
        
    public function getEmbed($sourceUrl)
        {
        $this->analyser->load($sourceUrl);
        return $this->analyser->getEmbed();
                }
                
                    
                
            
        
    
    


        
        

        
    }
