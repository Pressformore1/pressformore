<?php

namespace P4M\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','text')
            ->add('email','email')
            ->add('firstName','text')
            ->add('lastName','text')
            ->add('language', 'choice', [
                'choices' => [
                    'en' => 'English',
                    'fr' => 'Français'
                ],
                'multiple' => false

            ])
            ->add('title','text')
            ->add('website','url')
//                ->add('categories')
//            ->add('categories', 'p4m_corebundle_usercategory', array(
//            'class' => 'P4MCoreBundle:Category',
//                'multiple'=>true
////            'property' => 'CategoryTypeTitle'
//        ))
//            ->add('tags')
            ->add('address','text',['attr'=>['placeholder'=>'Steet and number']])
            ->add('city','text',['attr'=>['placeholder'=>'City']])
            ->add('country',null,['attr'=>['placeholder'=>'Country']])
            ->add('birthDate','date', array('years' => range(date('Y') -132, date('Y'))))
            ->add('website','url',['required'=>false])
            ->add('bio','textarea',['required'=>false])
            ->add('picture',new ImageType())
            ->add('publicStatus',new \P4M\UserBundle\Form\UserPublicStatusType())
            ->add('skills','userskills',array('required'=>false,'attr'=>['placeholder'=>'writing, video editing,datajournalism,...']))
            ->add('plainPassword','repeated',array('type' => 'password','first_options'  => array('attr' => array('placeholder' => 'Your New Password')),
    'second_options' => array('attr' => array('placeholder' => 'Confirm New Password'))));
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
