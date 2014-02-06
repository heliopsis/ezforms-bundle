<?php
/**
 * Class ServiceAliasesPass
 *
 * Sets which facade and provider services should be used by form controller
 * Service names are loaded from container parameters which are themselves set in HeliopsisEzFormsExtension
 *
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
        $this->setServiceAlias( $container, HeliopsisEzFormsExtension::FACADE_SERVICE_ID );
        $this->setServiceAlias( $container, HeliopsisEzFormsExtension::FORM_PROVIDER_SERVICE_ID );
        $this->setServiceAlias( $container, HeliopsisEzFormsExtension::HANDLER_PROVIDER_SERVICE_ID );
        $this->setServiceAlias( $container, HeliopsisEzFormsExtension::RESPONSE_PROVIDER_SERVICE_ID );
    }

    /**
     * @param ContainerBuilder $container
     * @param $aliasName
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function setServiceAlias( ContainerBuilder $container, $aliasName )
    {
        if ( !$container->hasParameter( $aliasName ) )
        {
            throw new InvalidConfigurationException( sprintf( 'Service %s is not defined', $aliasName ) );
        }

        $alias = $container->getParameter( $aliasName );
        if ( !$alias )
        {
            //alias is not configured
            return;
        }

        if ( $alias === $aliasName )
        {
            throw new InvalidConfigurationException( sprintf( 'Service %s cannot be an alias for itself', $alias ) );
        }

        if ( !$container->hasDefinition( $alias ) )
        {
            throw new InvalidConfigurationException( printf( 'Service %s is not defined', $alias ) );
        }

        $container->setAlias( $aliasName, $alias );

    }
}
