<?php
/**
 * User: jonas
 * Date: 2013-03-02
 * Time: 17:27
 *
 * Use with love
 */

namespace SKCMS\CKFinderBundle\Form\Type;


use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class CKFinderPopupType extends CKEditorType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	parent::setDefaultOptions($resolver);

    	$resolver->replaceDefaults(array(
    		'config' => array(
	            'filebrowserBrowseUrl' => $this->router->generate('ckfinder_index'),
	            'filebrowserImageBrowseUrl' => $this->router->generate('ckfinder_index', array('type' => 'images')),
	            'filebrowserFlashBrowseUrl' => $this->router->generate('ckfinder_index', array('type' => 'flash')),
	            'filebrowserUploadUrl' => $this->router->generate('ckfinder_init', array(
	                'command' => 'QuickUpload', 
	                'type' => 'files', 
	                'service' => 'php'
	            )),
	            'filebrowserImageUploadUrl' => $this->router->generate('ckfinder_init', array(
	                'command' => 'QuickUpload', 
	                'type' => 'images', 
	                'service' => 'php'
	            )),
	            'filebrowserFlashUploadUrl' => $this->router->generate('ckfinder_init', array(
	        		'command' => 'QuickUpload', 
	        		'type' => 'flash', 
	        		'service' => 'php'
	        	))
        	))
		);
    }

    public function getName()
    {
        return 'ckfinderpopup';
    }

    public function setRouter ($router) {
    	$this->router = $router;
    }
}