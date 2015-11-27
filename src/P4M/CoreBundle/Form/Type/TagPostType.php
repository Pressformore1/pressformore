<?php
namespace P4M\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\DataTransformer\TagsTransformer;
use P4M\CoreBundle\Form\DataTransformer\NameToTagTransformer;
use Symfony\Component\Form\FormBuilderInterface;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TagPostType
 *
 * @author Jona
 */
class TagPostType extends AbstractType
{
    private $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        
        $modelTransformer = new NameToTagTransformer($this->em);

        // ajoute un champ texte normal, mais y ajoute aussi votre convertisseur
        $builder->addModelTransformer($modelTransformer);
        $builder->addViewTransformer(new TagsTransformer());
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => '',
        ));
    }

 
    public function getParent()
    {
        return 'text';
    }
 
    public function getName()
    {
        return 'posttags';
    }

    
}