<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root( 'heliopsis_ezforms' );
        $rootNode->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode( 'facade' )
                        ->info( 'FormFacadeInterface service ID to use in controller' )
                        ->defaultValue( 'heliopsis_ezforms.facade.default' )
                        ->end()
                    ->arrayNode( 'providers' )
                        ->info( "These providers are automatically injected into default facade" )
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode( 'form' )
                                ->info( 'FormProvider service to use as heliopsis_ezforms.form_provider' )
                                ->defaultNull()
                                ->end()
                            ->scalarNode( 'handler' )
                                ->info( 'HandlerProvider service to use as heliopsis_ezforms.handler_provider' )
                                ->defaultNull()
                                ->end()
                            ->scalarNode( 'response' )
                                ->info( 'ResponseProvider service to use as heliopsis_ezforms.response_provider' )
                                ->defaultValue( 'heliopsis_ezforms.response_provider.redirect_confirm' )
                                ->end()
                            ->end()
                        ->end()
                    ->end();

        return $treeBuilder;
    }

}
