<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Provider\Form;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

class ContentRemoteIdMap implements FormProviderInterface
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
     * @param FormFactoryInterface $formFactory
     */
    function __construct( $formFactory )
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $contentRemoteId
     * @param string|FormTypeInterface $formType
     */
    public function addFormType( $contentRemoteId, $formType )
    {
        $this->map[$contentRemoteId] = $formType;
    }

    /**
     * @inheritdoc
     */
    public function getForm( Location $location )
    {
        if ( !isset( $this->map[$location->contentInfo->remoteId] ) )
        {
            throw new UnknownFormException(
                sprintf( "No form could be mapped to content remote id %s", $location->contentInfo->remoteId )
            );
        }

        return $this->formFactory->create( $this->map[$location->contentInfo->remoteId] );
    }
}
