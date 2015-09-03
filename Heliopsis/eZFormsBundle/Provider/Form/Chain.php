<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Provider\Form;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;

/**
 * Class Chain
 * @package Heliopsis\eZFormsBundle\Provider\Form
 */
class Chain implements FormProviderInterface
{
    /**
     * @var FormProviderInterface[][]
     */
    private $providers = array();

    /**
     * @param FormProviderInterface $formProvider
     * @param int $priority
     */
    public function addProvider( FormProviderInterface $formProvider, $priority = 0 )
    {
        if ( !array_key_exists( $priority, $this->providers ) )
        {
            $this->providers[$priority] = array();
        }

        if ( !in_array( $formProvider, $this->providers[$priority] ) )
        {
            $this->providers[$priority][] = $formProvider;
        }

        krsort( $this->providers );
    }

    /**
     * @param Location $location
     * @param string $viewType
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException
     */
    public function getForm( Location $location, $viewType )
    {
        foreach ( $this->providers as $providersPriority )
        {
            foreach ( $providersPriority as $provider )
            {
                try
                {
                    return $provider->getForm( $location );
                }
                catch ( UnknownFormException $e)
                {
                    continue;
                }
            }
        }

        throw new UnknownFormException( "No form could be mapped" );
    }
}
