<?php

namespace P4M\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TempAuthorType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('twitter',null,['required'=>false])
            ->add('email',null,['required'=>false])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\CoreBundle\Entity\TempAuthor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_corebundle_tempauthor';
    }
}
