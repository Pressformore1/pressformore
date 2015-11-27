<?php

/**
 * Description of NameToTagTransformer
 *
 * @author Jona
 */

namespace P4M\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use P4M\CoreBundle\Entity\Tag;

class NameToTagTransformer implements DataTransformerInterface{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (tag) to a string (name).
     *
     * @param  array|null $tags
     * @return array
     */
    public function transform($tags)
    {
        if (null === $tags) {
            return array();
        }
        $tagsname = array();
        
        foreach ($tags as $tag)
        {
            $tagsname[] = $tag->getName();
        }
        

        return $tagsname;
    }

    /**
     * Transforms a string (name) to an object (tag).
     *
     * @param  array $names
     * @return array
     * 
     */
    public function reverseTransform($names)
    {
        if (!$names) {
            return null;
        }
        
        $tagRepo = $this->em->getRepository('P4MCoreBundle:Tag');
        $tagList = array();
        foreach ($names as $name)
        {
            $tag = $tagRepo->findOneBy(array('name' => $name));
            if (null === $tag) 
            {
                $tag = new Tag();
                $tag->setName($name);
            }
            
            $tagList[]=$tag;
        }
        

        

        return $tagList;
    }
}

