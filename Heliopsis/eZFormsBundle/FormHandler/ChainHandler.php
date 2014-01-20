<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\FormHandler;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * Class ChainHandler
 * Chaîne le traitement de plusieurs handlers
 * @package Heliopsis\eZFormsBundle\FormHandler
 */
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
     * Ajoute un handler en bout de chaine
     * @param FormHandlerInterface $handler
     */
    public function addHandler( FormHandlerInterface $handler )
    {
        $this->handlers[] = $handler;
    }

    /**
     * Exécute le traitement
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
     * Ajout le `$content` aux handlers implémentants `ContentAwareHandler`
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
