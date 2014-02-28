<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Provider\Handler;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\FormHandler\ContentAwareHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\LocationAwareHandlerInterface;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;

class SingleHandlerProvider implements HandlerProviderInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $handler;

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @param FormHandlerInterface $handler
     * @param ContentService $contentService
     */
    function __construct( FormHandlerInterface $handler, ContentService $contentService )
    {
        $this->handler = $handler;
        $this->contentService = $contentService;
    }

    /**
     * Returns form handler to use at $location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        if ( $this->handler instanceof LocationAwareHandlerInterface )
        {
            $this->handler->setLocation( $location );
        }

        if ( $this->handler instanceof ContentAwareHandlerInterface )
        {
            $this->handler->setContent( $this->contentService->loadContentByContentInfo( $location->contentInfo ) );
        }

        return $this->handler;
    }
}
