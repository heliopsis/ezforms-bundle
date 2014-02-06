<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Controller;

use eZ\Publish\Core\MVC\Symfony\Controller\Content\ViewController;
use eZ\Publish\Core\MVC\Symfony\View\ViewManagerInterface;
use Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use Exception;

class FormController extends ViewController
{
    /**
     * @var \Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface
     */
    private $formFacade;

    /**
     * @param FormFacadeInterface $formFacade
     * @param ViewManagerInterface $viewManager
     */
    public function __construct( FormFacadeInterface $formFacade, ViewManagerInterface $viewManager )
    {
        parent::__construct( $viewManager );
        $this->formFacade = $formFacade;
    }

    /**
     * Main action for viewing content through a location in the repository.
     * Response will be cached with HttpCache validation model (Etag)
     *
     * @param int $locationId
     * @param string $viewType
     * @param boolean $layout
     * @param array $params
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @see \eZ\Publish\Core\MVC\Symfony\Controller\Content\ViewController::viewLocation
     */
    public function formAction( $locationId, $viewType, $layout = false, array $params = array() )
    {
        $this->performAccessChecks();
        $response = $this->buildResponse();

        try
        {
            $response->headers->set( 'X-Location-Id', $locationId );

            $request = $this->getRequest();
            $location = $this->getRepository()->getLocationService()->loadLocation( $locationId );
            $form = $this->formFacade->getForm( $location );

            if ( 'POST' === $request->getMethod() )
            {
                $form->submit( $this->getRequest() );
                if ( $form->isValid() )
                {
                    $data = $form->getData();
                    $handler = $this->formFacade->getHandler( $location );
                    $handler->handle( $data );

                    return $this->formFacade->getResponse( $location, $data );
                }
            }

            $response->setContent(
                $this->viewManager->renderLocation(
                    $location,
                    $viewType,
                    $params + array(
                        'noLayout' => !$layout,
                        'form' => $form->createView(),
                    )
                )
            );

            return $response;
        }
        catch ( Exception $e )
        {
            return $this->handleViewException( $response, $params, $e, $viewType, null, $locationId );
        }
    }

    /**
     * Build the response so that depending on settings it's cacheable
     *
     * @param string|null $etag
     * @param \DateTime|null $lastModified
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @see \eZ\Publish\Core\MVC\Symfony\Controller\Content\ViewController::buildResponse
     */
    protected function buildResponse( $etag = null, DateTime $lastModified = null )
    {
        if ( 'POST' === $this->getRequest()->getMethod() )
        {
            $response = new Response();
            $response->setPrivate();

            return $response;
        }
        else return parent::buildResponse( $etag, $lastModified );
    }

}
