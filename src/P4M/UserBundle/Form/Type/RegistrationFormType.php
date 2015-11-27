<?php
  
namespace P4M\UserBundle\Form\Type;
  
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
  
class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
          ->add('firstName', 'text')
          ->add('lastName', 'text')
          ->add('termsAccepted','checkbox')        

          
        ;
//        $builder->add('invitation', 'p4m_invitation_type');
    }
  
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\UserBundle\Entity\User'
        ));
    }
  
    public function getName()
    {
        return 'p4m_user_registration';
    }
}