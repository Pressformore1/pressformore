<?php
namespace P4M\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\UserBundle\Form\DataTransformer\SkillsTransformer;
use P4M\UserBundle\Form\DataTransformer\NameToSkillTransformer;
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
class UserSkillType extends AbstractType
{
    private $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
    
    
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        
        $modelTransformer = new NameToSkillTransformer($this->em);

        // ajoute un champ texte normal, mais y ajoute aussi votre convertisseur
        $builder->addModelTransformer($modelTransformer);
        $builder->addViewTransformer(new SkillsTransformer());
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
        return 'userskills';
    }

    
}