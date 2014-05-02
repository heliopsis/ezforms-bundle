<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Controller;

use Heliopsis\eZFormsBundle\Controller\FormController;
use Symfony\Component\DependencyInjection\Container;

class FormControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormController
     */
    private $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFacade;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockViewManager;

    /**
     * @var int
     */
    private $dummyLocationId = 2;

    /**
     * @var int
     */
    private $dummyCacheTTL = 30;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLocation;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockForm;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormView;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRequest;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContentService;

    public function setUp()
    {
        $this->mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $this->mockFormView = $this->getMock( 'Symfony\\Component\\Form\\FormView' );

        $this->mockForm = $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockForm->expects( $this->any() )
            ->method( 'createView' )
            ->will( $this->returnValue( $this->mockFormView ) );

        $this->mockFacade = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormFacade\\DefaultFormFacade' );
        $this->mockFacade->expects( $this->any() )
            ->method( 'getForm' )
            ->with( $this->mockLocation )
            ->will( $this->returnValue( $this->mockForm ) );

        $this->mockViewManager = $this->getMockBuilder( 'eZ\\Publish\\Core\\MVC\\Symfony\\View\\Manager' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockContentService = $this->getMockBuilder( 'eZ\\Publish\\API\\Repository\\ContentService' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new FormController( $this->mockFacade, $this->mockViewManager, $this->mockContentService );
    }

    private function setupContainer( $requestMethod = 'GET', $securityGranted = true )
    {
        $mockLocationService = $this->getMock( 'eZ\\Publish\\API\\Repository\\LocationService' );
        $mockLocationService->expects( $this->any() )
            ->method( 'loadLocation' )
            ->with( $this->equalTo( $this->dummyLocationId ) )
            ->will( $this->returnValue( $this->mockLocation ) );

        $mockRepository = $this->getMock( 'eZ\\Publish\\API\\Repository\\Repository' );
        $mockRepository->expects( $this->any() )
            ->method( 'getLocationService' )
            ->will( $this->returnValue( $mockLocationService ) );

        $this->mockRequest = $this->getMock( 'Symfony\\Component\\HttpFoundation\\Request' );
        $this->mockRequest->expects( $this->any() )
            ->method( 'getMethod' )
            ->will( $this->returnValue( $requestMethod ) );

        $mockContext = $this->getMock( 'Symfony\\Component\\Security\\Core\\SecurityContextInterface' );
        $mockContext->expects( $this->once() )
            ->method( 'isGranted' )
            ->will( $this->returnValue( $securityGranted ) );

        $mockConfigResolver = $this->getMock( 'eZ\\Publish\\Core\\MVC\\ConfigResolverInterface' );
        $mockConfigResolver->expects( $this->any() )
            ->method( 'hasParameter' )
            ->will(
                $this->returnValueMap(
                    array(
                        array( 'content.view_cache', null, null, true ),
                        array( 'content.ttl_cache', null, null, true ),
                        array( 'content.default_ttl', null, null, true ),
                    )
                )
            );

        $mockConfigResolver->expects( $this->any() )
            ->method( 'getParameter' )
            ->will(
                $this->returnValueMap(
                    array(
                        array( 'content.view_cache', null, null, true ),
                        array( 'content.ttl_cache', null, null, true ),
                        array( 'content.default_ttl', null, null, $this->dummyCacheTTL ),
                    )
                )
            );

        $mockDispatcher = $this->getMock( 'Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' );

        $this->container = new Container();
        $this->container->set( 'ezpublish.api.repository', $mockRepository );
        $this->container->set( 'security.context', $mockContext );
        $this->container->set( 'request', $this->mockRequest );
        $this->container->set( 'ezpublish.config.resolver', $mockConfigResolver );
        $this->container->set( 'event_dispatcher', $mockDispatcher );

        $this->controller->setContainer( $this->container );
    }

    public function testDisplayForm()
    {
        $this->setupContainer( 'GET', true );
        $viewType = 'view_type';
        $layout = true;
        $params = array(
            'dummy' => 1,
            'params' => 'to test',
        );

        $expectedViewParams = $params + array(
            'noLayout' => !$layout,
            'form' => $this->mockFormView,
        );

        $this->mockViewManager->expects( $this->once() )
            ->method( 'renderLocation' )
            ->with(
                $this->identicalTo( $this->mockLocation ),
                $this->equalTo( $viewType ),
                $this->identicalTo( $expectedViewParams )
            )
            ->will( $this->returnValue( '<form>' ) );

        $response = $this->controller->formAction( $this->dummyLocationId, $viewType, $layout, $params );

        $this->assertInstanceOf( 'Symfony\\Component\\HttpFoundation\Response', $response );
        $this->assertFalse( $response->headers->hasCacheControlDirective( 'private' ) );
        $this->assertEquals( $this->dummyCacheTTL, $response->getMaxAge() );
        $this->assertEquals( $this->dummyLocationId, $response->headers->get( 'X-Location-Id' ) );
        $this->assertEquals( 200, $response->getStatusCode() );
        $this->assertEquals( '<form>', $response->getContent() );
    }

    public function testDisplayForbiddenForm()
    {
        $this->setupContainer( 'GET', false );

        $this->setExpectedException( 'Symfony\\Component\\Security\\Core\\Exception\\AccessDeniedException' );
        $this->controller->formAction( $this->dummyLocationId, 'view_type' );
    }

    public function testInvalidFormData()
    {
        $this->setupContainer( 'POST' );

        $this->mockForm->expects( $this->once() )
            ->method( 'submit' )
            ->with( $this->mockRequest );

        $this->mockForm->expects( $this->once() )
            ->method( 'isValid' )
            ->will( $this->returnValue( false ) );

        $viewType = 'view_type';
        $layout = true;
        $params = array(
            'dummy' => 1,
            'params' => 'to test',
        );

        $expectedViewParams = $params + array(
            'noLayout' => !$layout,
            'form' => $this->mockFormView,
        );

        $this->mockViewManager->expects( $this->once() )
            ->method( 'renderLocation' )
            ->with(
                $this->identicalTo( $this->mockLocation ),
                $this->equalTo( $viewType ),
                $this->identicalTo( $expectedViewParams )
            )
            ->will( $this->returnValue( '<form error>' ) );

        $response = $this->controller->formAction( $this->dummyLocationId, $viewType, $layout, $params );

        $this->assertInstanceOf( 'Symfony\\Component\\HttpFoundation\Response', $response );
        $this->assertTrue( $response->headers->hasCacheControlDirective( 'private' ) );
        $this->assertEquals( $this->dummyLocationId, $response->headers->get( 'X-Location-Id' ) );
        $this->assertEquals( 200, $response->getStatusCode() );
        $this->assertEquals( '<form error>', $response->getContent() );
    }

    public function testValidFormData()
    {
        $mockData = new \StdClass();
        $mockData->attribute = 'mock data';

        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $mockHandler->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $mockData ) );

        $this->setupHandlerTest( $mockHandler, $mockData );

        $mockResponse = $this->getMock( 'Symfony\\Component\\HttpFoundation\\Response' );
        $this->mockFacade->expects( $this->once() )
            ->method( 'getResponse' )
            ->with( $this->identicalTo( $this->mockLocation ), $this->identicalTo( $mockData ) )
            ->will( $this->returnValue( $mockResponse ) );

        $response = $this->controller->formAction( $this->dummyLocationId, 'view_type' );
        $this->assertSame( $mockResponse, $response );
    }

    private function setupHandlerTest( $mockHandler, $mockData )
    {
        $this->setupContainer( 'POST' );

        $this->mockForm->expects( $this->once() )
            ->method( 'submit' )
            ->with( $this->mockRequest );

        $this->mockForm->expects( $this->once() )
            ->method( 'isValid' )
            ->will( $this->returnValue( true ) );

        $this->mockForm->expects( $this->once() )
            ->method( 'getData' )
            ->will( $this->returnValue( $mockData ) );

        $this->mockFacade->expects( $this->once() )
            ->method( 'getHandler' )
            ->with( $this->identicalTo( $this->mockLocation ) )
            ->will( $this->returnValue( $mockHandler ) );
    }

    public function testHandlerLocationInjection()
    {
        $mockData = new \StdClass();
        $mockData->attribute = 'mock data';

        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\LocationAwareHandlerInterface' );
        $mockHandler->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $mockData ) );

        $this->setupHandlerTest( $mockHandler, $mockData );

        $mockHandler->expects( $this->once() )
            ->method( 'setLocation' )
            ->with( $this->identicalTo( $this->mockLocation ) );

        $this->controller->formAction( $this->dummyLocationId, 'view_type' );
    }

    public function testHandlerContentInjection()
    {
        $mockData = new \StdClass();
        $mockData->attribute = 'mock data';

        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\ContentAwareHandlerInterface' );
        $mockHandler->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $mockData ) );

        $this->setupHandlerTest( $mockHandler, $mockData );

        $mockContentInfo = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\ContentInfo' );
        $mockContent = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Content' );

        $this->mockLocation->expects( $this->once() )
            ->method( '__get' )
            ->with( 'contentInfo' )
            ->will( $this->returnValue( $mockContentInfo ) );

        $this->mockContentService->expects( $this->once() )
            ->method( 'loadContentByContentInfo' )
            ->with( $mockContentInfo )
            ->will( $this->returnValue( $mockContent ) );

        $mockHandler->expects( $this->once() )
            ->method( 'setContent' )
            ->with( $this->identicalTo( $mockContent ) );

        $this->controller->formAction( $this->dummyLocationId, 'view_type' );
    }
}
