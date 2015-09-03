<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Provider\Form;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class ContentTypeIdentifierMap
 * @package Heliopsis\eZFormsBundle\Provider\Form
 */
class ContentTypeIdentifierMap implements FormProviderInterface
{
    /**
     * @var array
     */
    private $map = array();

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ContentTypeService
     */
    private $contentTypeService;

    /**
     * @param FormFactoryInterface $formFactory
     * @param ContentTypeService $contentTypeService
     */
    function __construct( FormFactoryInterface $formFactory, ContentTypeService $contentTypeService)
    {
        $this->formFactory = $formFactory;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param string $contentTypeIdentifier
     * @param string|FormTypeInterface $formType
     */
    public function addFormType( $contentTypeIdentifier, $formType )
    {
        $this->map[$contentTypeIdentifier] = $formType;
    }

    /**
     * @param Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @param string $viewType
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException
     */
    public function getForm( Location $location, $viewType )
    {
        $locationContentTypeId = $location->contentInfo->contentTypeId;

        /** @var ContentType $locationContentType */
        $locationContentType = $this->contentTypeService->loadContentType( $locationContentTypeId );

        if ( !array_key_exists( $locationContentType->identifier, $this->map ) )
        {
            throw new UnknownFormException(
                sprintf( "No form could be mapped to content type identifier '%s'", $locationContentType->identifier )
            );
        }

        return $this->formFactory->create( $this->map[$locationContentType->identifier] );
    }
}
