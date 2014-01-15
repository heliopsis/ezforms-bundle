<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\DependencyInjection\Compiler;

use Heliopsis\eZFormsBundle\DependencyInjection\HeliopsisEzFormsExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ServiceAliasesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->setServiceAlias($container, HeliopsisEzFormsExtension::FACADE_SERVICE_ID);
        $this->setServiceAlias($container, HeliopsisEzFormsExtension::FORM_PROVIDER_SERVICE_ID);
        $this->setServiceAlias($container, HeliopsisEzFormsExtension::HANDLER_PROVIDER_SERVICE_ID);
        $this->setServiceAlias($container, HeliopsisEzFormsExtension::RESPONSE_PROVIDER_SERVICE_ID);
    }

    /**
     * @param ContainerBuilder $container
     * @param $aliasName
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function setServiceAlias( ContainerBuilder $container, $aliasName )
    {
        if( !$container->hasParameter( $aliasName ) )
        {
            throw new InvalidConfigurationException( 'Service ' . $aliasName . ' is not defined' );
        }

        $alias = $container->getParameter( $aliasName );
        if( !$alias )
        {
            //alias is not configured
            return;
        }

        if( $alias === $aliasName )
        {
            throw new InvalidConfigurationException( 'Invalid service: ' . $alias );
        }

        if( !$container->hasDefinition( $alias ) )
        {
            throw new InvalidConfigurationException( 'Service could not be found: ' . $alias );
        }

        $container->setAlias( $aliasName, $alias );

    }
}