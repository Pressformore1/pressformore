<?php

namespace P4M\ModerationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FlagTypeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('level', 'choice', array(
                'choices'   => array(1,2,3,4,5,6,7,8,9,10),
                
            ))
            ->add('description')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\ModerationBundle\Entity\FlagType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_moderationbundle_flagtype';
    }
}
