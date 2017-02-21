<?php

namespace P4M\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\Type\TagPostType;

class PostType extends AbstractType
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
            ->add('title', null, [
                'attr' => ['class'=>"col-xs-20"],
                'required' => true
            ])
            ->add('picture','choice', [
                'choices'   => $this->selectNameToValue($this->pictureList),
                'data' => $this->picture,
                'multiple'  => false
            ])
            ->add('content', null, array(
                'attr' => array( 'class'=>"col-xs-20", 'rows'=>'6','maxlength'=>320)
            ))
            ->add('sourceUrl','hidden')
//            ->add('author')
            ->add('lang', null, array(
                'attr' => array( 'class'=>"col-xs-10")
            ))
            ->add('type', null, array(
                'attr' => array( 'class'=>"col-xs-10")
            ))
//            ->add('categories', null, array(
//                'attr' => array( 'class'=>"chosen-select", 'data-placeholder'=>"Find the category")
//                ))
            ->add('categories', 'p4m_corebundle_usercategory', [
                'class' => 'P4MCoreBundle:Category',
                'multiple'=>true
                //'property' => 'CategoryTypeTitle'
            ])
            ->add('tags','posttags', [
                'attr' => ['class'=>"col-xs-20"],
            ])
            ->add('pictureList','collection',array(
                'allow_add' => true,
            ))
            ->add('anchors','collection',array(
                'allow_add' => true, 
                'required'=>false
            ))
            ->add('blogPost','checkbox',array(
                'required'=>false
            ))
            ->add('embed','hidden')
            ->add('iframeAllowed',null,['required'=>false])
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
        $resolver->setDefaults([
            'data_class' => 'P4M\CoreBundle\Entity\Post',
            'csrf_protection'   => false,
        ]);
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
