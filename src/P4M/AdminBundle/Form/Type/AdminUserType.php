<?php

namespace P4M\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\ImageType;

class AdminUserType extends AbstractType
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
        $roles = array('ROLE_USER'=>'Utilisateur','ROLE_AUTEUR'=>'auteur','ROLE_MODERATEUR'=>'Modérateur','ROLE_ADMIN'=>'Admin');
//        die(print_r($this->editor->getRoles()));
        if (in_array('ROLE_SUPER_ADMIN', $this->editor->getRoles()))
        {
            $roles['ROLE_SUPER_ADMIN']='Super Admin';
        }
        $builder
            ->add('locked','checkbox',array('required' => false))
            ->add('roles','choice',array(
                'choices'   => $roles,
                'multiple'  => true
                ))
            
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_userbundle_user';
    }
}
