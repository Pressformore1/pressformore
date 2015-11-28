<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new P4M\CoreBundle\P4MCoreBundle(),
            new P4M\UserBundle\P4MUserBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new P4M\AdminBundle\P4MAdminBundle(),
            new P4M\ModerationBundle\P4MModerationBundle(),
            new P4M\TrackingBundle\P4MTrackingBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
           new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new P4M\BackofficeBundle\P4MBackofficeBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new P4M\PinocchioBundle\P4MPinocchioBundle(),
            new P4M\NotificationBundle\P4MNotificationBundle(),
            new P4M\MangoPayBundle\P4MMangoPayBundle(),
            new P4M\ContactBundle\P4MContactBundle(),
            new P4M\BlogBundle\P4MBlogBundle(),
            new P4M\MyElasticaBundle\P4MMyElasticaBundle(),
            new SKCMS\CoreBundle\SKCMSCoreBundle(),
            new SKCMS\AdminBundle\SKCMSAdminBundle(),
            new P4M\ConsoleBundle\P4MConsoleBundle(),
            new Kayue\EssenceBundle\KayueEssenceBundle(),
            );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
            
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
    
}
