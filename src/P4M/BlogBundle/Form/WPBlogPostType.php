<?php

namespace P4M\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WPBlogPostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('picture','ckfinder')
            ->add('sourceUrl')
            ->add('content','textarea')
            ->add('anchors')
//            ->add('date')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\BlogBundle\Entity\WPBlogPost'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_blogbundle_wpblogpost';
    }
}
