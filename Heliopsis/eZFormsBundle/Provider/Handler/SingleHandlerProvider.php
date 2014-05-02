<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Provider\Handler;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;

class SingleHandlerProvider implements HandlerProviderInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $handler;

    /**
     * @param FormHandlerInterface $handler
     */
    function __construct( FormHandlerInterface $handler )
    {
        $this->handler = $handler;
    }

    /**
     * Returns form handler to use at $location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        return $this->handler;
    }
}
