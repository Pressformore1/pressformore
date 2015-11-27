<?php

namespace P4M\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\ImageType;

class AdminCategoryType extends AbstractType
{
    
    private $editor;
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    
    public function __construct(\P4M\UserBundle\Entity\User $editor)
    {
        $this->editor = $editor;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('name')
            ->add('description')
            ->add('iconColor','ckfinderpopup')
            ->add('iconGrey','ckfinderpopup')
            ->add('draft','checkbox',array('required' => false))
//            ->add('icon','file')
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\CoreBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_adminbundle_category';
    }
}
