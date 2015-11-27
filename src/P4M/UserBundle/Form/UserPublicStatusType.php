<?php

namespace P4M\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserPublicStatusType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','choice',array('choices' => array(
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC
                
                )
                )
        
    )
            ->add('name','choice',array('choices' => array(
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC
                
                )
                ))
            ->add('website','choice',array('choices' => array(
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC
                
                )
                ))
            ->add('localisation','choice',array('choices' => array(
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PRIVATE,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_FOLLOWERS,
                    \P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC=>\P4M\UserBundle\Entity\UserPublicStatus::STATUS_PUBLIC
                
                )
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\UserBundle\Entity\UserPublicStatus'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_userbundle_userpublicstatus';
    }
}
