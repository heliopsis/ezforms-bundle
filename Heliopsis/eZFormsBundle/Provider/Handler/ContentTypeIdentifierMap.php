<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Provider\Handler;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\NullHandler;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;

/**
 * Class ContentTypeIdentifierMap
 * @package Heliopsis\eZFormsBundle\Provider\Handler
 */
class ContentTypeIdentifierMap implements HandlerProviderInterface
{
    /**
     * @var FormHandlerInterface[]
     */
    private $map = array();

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @param ContentTypeService $contentTypeService
     */
    function __construct( ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param string $contentTypeIdentifier
     * @param FormHandlerInterface $formHandler
     */
    public function addFormHandler( $contentTypeIdentifier, FormHandlerInterface $formHandler )
    {
        $this->map[$contentTypeIdentifier] = $formHandler;
    }

    /**
     * Returns form handler to use at $location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        $locationContentTypeId = $location->contentInfo->contentTypeId;

        /** @var ContentType $locationContentType */
        $locationContentType = $this->contentTypeService->loadContentType( $locationContentTypeId );

        return array_key_exists( $locationContentType->identifier, $this->map ) ?
            $this->map[$locationContentType->identifier] :
            new NullHandler();
    }
}
