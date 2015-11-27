<?php

namespace P4M\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\Type\TagPostType;

class PostAdminType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    private $pictureList = array();
    private $picture ;
    
    
    public function __construct(array $pictureList= array(),$picture='')
    {
        $this->pictureList = $pictureList;
        $this->picture = $picture;
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array(
                'attr' => array( 'class'=>"col-xs-20")
            ))
           ->add('iframeAllowed',null,['required'=>false])
           ->add('quarantaine',null,['required'=>false])
        ;
    }
    
    private function selectNameToValue($array)
    {
        $toReturn = array();
        foreach ($array as $value=>$name)
        {
            $toReturn[$name]=$name;
        }
        
        return $toReturn;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\CoreBundle\Entity\Post',
            'csrf_protection'   => false,
        ));
    }
//    
//    public function getDefaultOptions(array $options)
//    {
//        $options = parent::getDefaultOptions($options);
//        $options['csrf_protection'] = false;
//
//        return $options;
//    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_corebundle_post';
    }
}
