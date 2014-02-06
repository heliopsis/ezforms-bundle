<?php
/**
 * ChainHandler Class
 *
 * Allows to chain multiple handlers one after the other
 *
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;

class ChainHandler implements LocationAwareHandlerInterface
{

    private $handlers = array();

    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @param array $handlers
     */
    public function __construct( ContentService $contentService, array $handlers )
    {
        $this->contentService = $contentService;

        /**
         * @var FormHandlerInterface $handler
         */
        foreach ( $handlers as $handler )
        {
            $this->addHandler( $handler );
        }
    }

    /**
     * Adds a handler at the end of the chain
     * @param FormHandlerInterface $handler
     */
    public function addHandler( FormHandlerInterface $handler )
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function handle($data)
    {
        /**
         * @var FormHandlerInterface $handler
         */
        foreach ( $this->handlers as $handler )
        {
            $handler->handle( $data );
        }
    }

    /**
     * Passes content and/or location to location and/or content aware handlers
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     */
    public function setLocation( Location $location )
    {
        foreach ( $this->handlers as $handler )
        {
            if ( $handler instanceof LocationAwareHandlerInterface )
            {
                $handler->setLocation( $location );
            }

            if ( $handler instanceof ContentAwareHandlerInterface )
            {
                $handler->setContent( $this->contentService->loadContentByContentInfo( $location->contentInfo ) );
            }
        }
    }
}
