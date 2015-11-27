<?php
namespace P4M\CoreBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CategoryPostType
 *
 * @author Jona
 */
class CategoryPostType extends \Symfony\Component\Form\AbstractType
{
    
    private $em;

    public function __construct( \Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $catsRepo = $this->em->getRepository('P4MCoreBundle:Category');
        $categories = $catsRepo->findAllNotDeleted();
         
        $resolver->setDefaults(array(
            'choices' => $categories
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'post_categories';
    }
}
