<?php

namespace P4M\ModerationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostFlagType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('type','entity',array(
                    'class' => 'P4MModerationBundle:FlagType',
                    'property' => 'name',
                    'expanded'=>true
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\ModerationBundle\Entity\PostFlag'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_moderationbundle_postflag';
    }
}
