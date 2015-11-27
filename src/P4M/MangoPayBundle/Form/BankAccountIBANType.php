<?php

namespace P4M\MangoPayBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankAccountIBANType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iban')
            ->add('bic')
            ->add('ownerName')
            ->add('ownerAddress')
            ->add('tag')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\MangoPayBundle\Entity\BankAccountIBAN'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_mangopaybundle_bankaccountiban';
    }
}
