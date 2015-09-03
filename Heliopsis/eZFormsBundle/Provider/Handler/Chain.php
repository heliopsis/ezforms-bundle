<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Provider\Handler;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\FormHandler\NullHandler;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;

/**
 * Class Chain
 * @package Heliopsis\eZFormsBundle\Provider\Handler
 */
class Chain implements HandlerProviderInterface
{

    /**
     * @var HandlerProviderInterface[][]
     */
    private $providers = array();

    /**
     * @param HandlerProviderInterface $handlerProvider
     * @param int $priority
     */
    public function addProvider( HandlerProviderInterface $handlerProvider, $priority = 0 )
    {
        if ( !array_key_exists( $priority, $this->providers ) )
        {
            $this->providers[$priority] = array();
        }

        if ( !in_array( $handlerProvider, $this->providers[$priority] ) )
        {
            $this->providers[$priority][] = $handlerProvider;
        }

        krsort( $this->providers );
    }

    /**
     * Returns form handler to use at $location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $viewType
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location, $viewType )
    {
        foreach ( $this->providers as $providersPriority )
        {
            foreach ( $providersPriority as $provider )
            {
                $currentHandler = $provider->getHandler( $location );

                if ( !$currentHandler instanceof NullHandler )
                {
                    return $currentHandler;
                }
            }
        }

        return new NullHandler();
    }
}
