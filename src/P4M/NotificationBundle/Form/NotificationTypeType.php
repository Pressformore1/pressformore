<?php

namespace P4M\NotificationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationTypeType extends AbstractType
{
    
    private $typeLinks ;
    
    
    public function __construct(array $typeLinks)
    {
        $this->typeLinks = $typeLinks;
        
        
    }
    
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            //->add('dateAdded')
            ->add('message')
            ->add('link')
            ->add('icon','ckfinderpopup')
            ->add('typeLink','choice',array(
                'choices'   => $this->selectNameToValue($this->typeLinks),
                'multiple'  => false
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\NotificationBundle\Entity\NotificationType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_notificationbundle_notificationtype';
    }
    
    
    private function selectNameToValue($typeLinks)
    {
        
        $toReturn = array();
        foreach ($typeLinks as $value=>$name)
        {
            $toReturn[$name]=$name;
        }
        
        return $toReturn;
    }
}
