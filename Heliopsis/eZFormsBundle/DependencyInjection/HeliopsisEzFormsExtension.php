<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class HeliopsisEzFormsExtension extends Extension
{

    const FACADE_SERVICE_ID = 'heliopsis_ezforms.facade';
    const FORM_PROVIDER_SERVICE_ID = 'heliopsis_ezforms.form_provider';
    const HANDLER_PROVIDER_SERVICE_ID = 'heliopsis_ezforms.handler_provider';
    const RESPONSE_PROVIDER_SERVICE_ID = 'heliopsis_ezforms.response_provider';

    /**
     * Loads a specific configuration.
     *
     * @param array $configs    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration( $configuration, $configs );

        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );

        //Services to use are stored as parameters and will be processes by Compiler\ServiceAliasesPass
        $container->setParameter( self::FACADE_SERVICE_ID, $config['facade'] );
        $container->setParameter( self::FORM_PROVIDER_SERVICE_ID, $config['providers']['form'] );
        $container->setParameter( self::HANDLER_PROVIDER_SERVICE_ID, $config['providers']['handler'] );
        $container->setParameter( self::RESPONSE_PROVIDER_SERVICE_ID, $config['providers']['response'] );
    }

    public function getAlias()
    {
        return 'heliopsis_ezforms';
    }

}
