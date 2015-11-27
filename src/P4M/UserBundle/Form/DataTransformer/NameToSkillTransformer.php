<?php

/**
 * Description of NameToTagTransformer
 *
 * @author Jona
 */

namespace P4M\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use P4M\UserBundle\Entity\Skill;

class NameToSkillTransformer implements DataTransformerInterface{
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
    public function transform($skills)
    {
        if (null === $skills) {
            return array();
        }
        $skillsname = array();
        
        foreach ($skills as $skill)
        {
            $skillsname[] = $skill->getName();
        }
        

        return $skillsname;
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
        
        $skillRepo = $this->em->getRepository('P4MUserBundle:Skill');
        $skillList = array();
        foreach ($names as $name)
        {
            $skill = $skillRepo->findOneBy(array('name' => $name));
            if (null === $skill) 
            {
                $skill = new Skill();
                $skill->setName($name);
            }
            
            $skillList[]=$skill;
        }
        

        

        return $skillList;
    }
}

