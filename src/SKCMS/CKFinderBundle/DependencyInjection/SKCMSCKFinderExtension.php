<?php

namespace SKCMS\CKFinderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SKCMSCKFinderExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
//        $configuration = new Configuration();
//        $config = $this->processConfiguration($configuration, $configs);
//
//        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
//        $loader->load('services.yml');
        
//        die(print_r($configs,true));
        $configuration = new Configuration();
//        $configs['baseDir'] = '/uploads';
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach(array('services', 'form') as $service) {
            $loader->load(sprintf('%s.xml', $service));
        }

        if($config['service'] == 's3'){
            $loader->load('s3.xml');
        }
        else{
            $loader->load('connector.xml');
        }
        switch ($config['service']) {
            case 'php':
                $container->setAlias('jonlil_ckfinder.connector', 'jonlil.ckfinder.connector.php');
                break;
            case 's3':
                $container->setAlias('jonlil_ckfinder.connector', 'jonlil.ckfinder.connector.s3');
                $container->setParameter('jonlil_ckfinder.amazon', array(
                    'secret' => $config['secret'],
                    'bucket' => $config['bucket'],
                    'access_key' => $config['accessKey'],
                    'base_url' => $config['baseUrl'],
                    'base_dir' => $config['baseDir'],
                    'thumbnails_file' => $config['thumbnailsFile'],
                    'thumbnails_enabled' => $config['thumbnailsEnabled'],
                    'direct_access' => $config['directAccess'],
                    'file_delete' => $config['fileDelete'],
                    'file_rename' => $config['fileRename'],
                    'file_upload' => $config['fileUpload'],
                    'file_view' => $config['fileView'],
                    'folder_delete' => $config['folderDelete'],
                    'folder_rename' => $config['folderRename'],
                    'folder_create' => $config['folderCreate'],
                    'folder_view' => $config['folderView']
                ));
                $container->setParameter('jonlil_ckfinder.baseDir', $config['baseDir']);
                break;
        }

        $container->setParameter('jonlil_ckfinder.license', array(
            'key' => $config['license']['key'],
            'name' => $config['license']['name']
        ));

        $container->setParameter('jonlil_ckfinder.baseUrl', $config['baseUrl']);

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array('JonlilCKFinderBundle:Form:ckfinder_widget.html.twig')
        ));
    }
}
