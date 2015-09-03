<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\Security\Authorization\Attribute;
use Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface;
use Heliopsis\eZFormsBundle\FormHandler\ContentAwareHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\LocationAwareHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Exception;

class FormController extends Controller
{
    /**
     * @var FormFacadeInterface
     */
    private $formFacade;

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
        if( !$this->isGranted( new Attribute( 'content', 'read' ) ) )
        {
            throw new AccessDeniedException();
        }

        $request = $this->getRequest();
        $location = $this->getLocationService()->loadLocation( $locationId );
        $form = $this->getFormFacade()->getForm( $location );

        if ( 'POST' === $request->getMethod() )
        {
            $form->submit( $this->getRequest() );
            if ( $form->isValid() )
            {
                $data = $form->getData();
                $handler = $this->getHandler( $location );
                $handler->handle( $data );

                return $this->getFormFacade()->getResponse( $location, $data );
            }
        }

        $response = $this->container->get( 'ez_content' )->viewLocation(
            $locationId,
            $viewType,
            $layout,
            $params + array(
                'form' => $form->createView(),
            )
        );

        if ( 'POST' === $request->getMethod() )
        {
            $response->setPrivate();
        }

        return $response;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    private function getHandler( Location $location )
    {
        $handler = $this->getFormFacade()->getHandler( $location );
        if ( $handler instanceof LocationAwareHandlerInterface )
        {
            $handler->setLocation( $location );
        }

        if ( $handler instanceof ContentAwareHandlerInterface )
        {
            $handler->setContent( $this->getContentService()->loadContentByContentInfo( $location->contentInfo ) );
        }

        return $handler;
    }

    /**
     * @return \eZ\Publish\Core\Repository\LocationService
     */
    private function getLocationService()
    {
        return $this->container->get( 'ezpublish.api.service.location' );
    }

    /**
     * @return \eZ\Publish\Core\Repository\ContentService
     */
    private function getContentService()
    {
        return $this->container->get( 'ezpublish.api.service.content' );
    }

    /**
     * @return FormFacadeInterface
     */
    private function getFormFacade()
    {
        return $this->formFacade;
    }

    /**
     * @param \Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface $formFacade
     * @return void
     */
    public function setFormFacade( FormFacadeInterface $formFacade )
    {
        $this->formFacade = $formFacade;
    }
}
