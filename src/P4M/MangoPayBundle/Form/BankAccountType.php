<?php

namespace P4M\MangoPayBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankAccountType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankName')
            ->add('institutionNumber')
            ->add('branchCode')
            ->add('accountNumber')
            ->add('iBAN')
            ->add('bIC')
            ->add('aBA')
            ->add('sortCode')
            ->add('ownerName')
            ->add('ownerAddress')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\MangoPayBundle\Entity\BankAccount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_mangopaybundle_bankaccount';
    }
}
