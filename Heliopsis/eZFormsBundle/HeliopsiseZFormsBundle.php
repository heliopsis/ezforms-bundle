<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle;

use Heliopsis\eZFormsBundle\DependencyInjection\Compiler\ServiceAliasesPass;
use Heliopsis\eZFormsBundle\DependencyInjection\HeliopsisEzFormsExtension;
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
