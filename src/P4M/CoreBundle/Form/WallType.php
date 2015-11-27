<?php

namespace P4M\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use P4M\CoreBundle\Entity\TagRepository;

class WallType extends AbstractType
{
    private $posted;
    
    public function __construct($posted = false)
    {
        $this->posted = $posted;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $includedTagsId = [1];
        $excludedTagsId = [1];
        $wall = $builder->getData();
        foreach ($wall->getIncludedTags() as $tag)
        {
            $includedTagsId[]=$tag->getId();
        }
        foreach ($wall->getExcludedTags() as $tag)
        {
            $excludedTagsId[]=$tag->getId();
        }
        $builder
            ->add('name')
            ->add('description')
            ->add('picture',new ImageType())
//            ->add('dateAdded')
//            ->add('user')
                
//            ->add('includedCategories',null,array('multiple'=>true,'expanded'=>true))
//            ->add('excludedCategories',null,array('multiple'=>true,'expanded'=>true))
            ->add('includedCategories','p4m_corebundle_wallcategory', array(
                'class' => 'P4MCoreBundle:Category',
                'multiple'=>true,
                'expanded'=>true,
                'required'  => false
                
            ))
            
//            ->add('includedPeople',null,array('required'  => false))
            ->add('excludedCategories','p4m_corebundle_wallcategory', array(
                'class' => 'P4MCoreBundle:Category',
                'multiple'=>true,
                'expanded'=>true,
                'required'  => false
                //'property' => 'CategoryTypeTitle'
            ))
            
//            ->add('excludedPeople',null,array('required'  => false))
        ;
                if ($this->posted)
                {
//                    $builder->add('includedTags','p4m_corebundle_walltag', array(
//                        'class' => 'P4MCoreBundle:Tag',
//                        'multiple'=>true,
//                        'expanded'=>true,
//                        'required'  => false,
//                        
//                        //'property' => 'CategoryTypeTitle'
//                    ))
//                        ->add('excludedTags','p4m_corebundle_walltag', array(
//                            'class' => 'P4MCoreBundle:Tag',
//                            'multiple'=>true,
//                            'expanded'=>true,
//                            'required'  => false,
//                            
//                            //'property' => 'CategoryTypeTitle'
//                        ));
                    $builder->add('includedTags')
                        ->add('excludedTags');
                }
                else
                {
                    $builder->add('includedTags','p4m_corebundle_walltag', array(
                        'class' => 'P4MCoreBundle:Tag',
                        'multiple'=>true,
                        'expanded'=>true,
                        'required'  => false,
                        'query_builder' => function(TagRepository $repo) use($includedTagsId)
                        {
                            $qb = $repo->createQueryBuilder('t');
                            $qb->where($qb->expr()->in('t.id',$includedTagsId));

                            return $qb;
                        },
                        //'property' => 'CategoryTypeTitle'
                    ))
                        ->add('excludedTags','p4m_corebundle_walltag', array(
                        'class' => 'P4MCoreBundle:Tag',
                        'multiple'=>true,
                        'expanded'=>true,
                        'required'  => false,
                        'query_builder' => function(TagRepository $repo) use ($excludedTagsId)
                        {
                            $qb = $repo->createQueryBuilder('t');
                            $qb->where($qb->expr()->in('t.id',$excludedTagsId));

                            return $qb;
                        },
                        //'property' => 'CategoryTypeTitle'
                    ));
                }
        
       
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'P4M\CoreBundle\Entity\Wall'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'p4m_corebundle_wall';
    }
}
