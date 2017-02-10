<?php

namespace P4M\UserBundle\Form;

use P4M\CoreBundle\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('language', 'choice', [
                'choices' => [
                    'en' => 'English',
                    'fr' => 'Français'
                ],
                'multiple' => false

            ])
            ->add('title', 'text')
            ->add('address', 'text', [
                'attr' => [
                    'placeholder' => 'Steet and number'
                ]
            ])
            ->add('city', 'text', [
                'attr' => [
                    'placeholder' => 'City'
                ]
            ])
            ->add('country', null, ['attr' => ['placeholder' => 'Country']])
            ->add('birthDate', 'date', [
                'years' => range(date('Y') - 132, date('Y'))
            ])
            ->add('website', 'url', [
                'required' => false
            ])
            ->add('bio', 'textarea', [
                'required' => false
            ])
            ->add('picture', new ImageType())
            ->add('publicStatus', new \P4M\UserBundle\Form\UserPublicStatusType())
            ->add('skills', 'userskills', [
                'required' => false,
                'attr' => [
                    'placeholder' => 'writing, video editing,datajournalism,...'
                ]
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Your New Password'
                    ]
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Confirm New Password'
                    ]
                ]
            ]);
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
