<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle;


use Heliopsis\eZFormsBundle\DependencyInjection\Compiler\ServiceAliasesPass;
use Heliopsis\eZFormsBundle\DependencyInjection\HeliopsisEzFormsExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HeliopsiseZFormsBundle extends Bundle
{
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        $container->addCompilerPass( new ServiceAliasesPass() );
    }

    public function getContainerExtension()
    {
        return new HeliopsisEzFormsExtension();
    }
}