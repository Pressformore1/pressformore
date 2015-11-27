<?php

namespace P4M\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserCategoryType extends AbstractType
{
    

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_corebundle_usercategory';
    }
    
    public function getParent()
    {
        return 'entity';
    }
}
