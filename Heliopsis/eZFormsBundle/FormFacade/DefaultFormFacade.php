<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\FormFacade;

use eZ\Publish\API\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Exceptions\BadConfigurationException;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface;
use Heliopsis\eZFormsBundle\FormHandler\NullHandler;
use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;
use Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class DefaultFormFacade implements FormFacadeInterface
{
    /**
     * @var FormProviderInterface
     */
    protected $formProvider;

    /**
     * @var HandlerProviderInterface
     */
    protected $handlerProvider;

    /**
     * @var ResponseProviderInterface
     */
    protected $responseProvider;

    /**
     * @param FormProviderInterface $formProvider
     * @param HandlerProviderInterface $handlerProvider
     * @param ResponseProviderInterface $responseProvider
     */
    function __construct(FormProviderInterface $formProvider = null, HandlerProviderInterface $handlerProvider = null, ResponseProviderInterface $responseProvider = null)
    {
        $this->formProvider = $formProvider;
        $this->handlerProvider = $handlerProvider;
        $this->responseProvider = $responseProvider;
    }

    /**
     * @param \Heliopsis\eZFormsBundle\Provider\FormProviderInterface $formProvider
     * @return void
     */
    public function setFormProvider(FormProviderInterface $formProvider)
    {
        $this->formProvider = $formProvider;
    }

    /**
     * @param \Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface $handlerProvider
     * @return void
     */
    public function setHandlerProvider(HandlerProviderInterface $handlerProvider)
    {
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param \Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface $responseProvider
     * @return void
     */
    public function setResponseProvider(ResponseProviderInterface $responseProvider)
    {
        $this->responseProvider = $responseProvider;
    }

    /**
     * Returns symfony form to display at $location
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $viewType
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException if no form matches $location
     */
    public function getForm(Location $location, $viewType)
    {
        if ( null === $this->formProvider )
        {
            throw new UnknownFormException();
        }

        return $this->formProvider->getForm( $location, $viewType );
    }

    /**
     * Returns form handler to use at $location
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $viewType
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler(Location $location, $viewType)
    {
        if ( null === $this->handlerProvider )
        {
            return new NullHandler();
        }

        return $this->handlerProvider->getHandler( $location, $viewType );
    }

    /**
     * Creates HTTP Response to be returned by controller
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $viewType
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Heliopsis\eZFormsBundle\Exceptions\BadConfigurationException
     */
    public function getResponse(Location $location, $viewType, $data)
    {
        if ( null === $this->responseProvider )
        {
            throw new BadConfigurationException( "No Response Provider set in default FormFacade" );
        }

        return $this->responseProvider->getResponse( $location, $viewType, $data );
    }

}
