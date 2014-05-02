<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Provider\Handler;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\NullHandler;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;

class ContentRemoteIdMap implements HandlerProviderInterface
{
    /**
     * @var FormHandlerInterface[]|array
     */
    private $map = array();

    /**
     * @param string $contentRemoteId
     * @param FormHandlerInterface $formHandler
     */
    public function addFormHandler( $contentRemoteId, FormHandlerInterface $formHandler )
    {
        $this->map[$contentRemoteId] = $formHandler;
    }

    /**
     * Returns form handler to use at $location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        if ( isset( $this->map[$location->contentInfo->remoteId] ) )
        {
            return $this->map[$location->contentInfo->remoteId];
        }

        return new NullHandler();
    }
}
