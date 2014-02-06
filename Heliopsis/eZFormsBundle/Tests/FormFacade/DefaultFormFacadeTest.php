<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\FormFacade;

use Heliopsis\eZFormsBundle\FormFacade\DefaultFormFacade;

class DefaultFormFacadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockHandlerProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockResponseProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockForm;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockHandler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockResponse;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockLocation;

    /**
     * @var mixed
     */
    private $mockData;

    public function setUp()
    {
        $this->mockFormProvider = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\FormProviderInterface' );
        $this->mockHandlerProvider = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\HandlerProviderInterface' );
        $this->mockResponseProvider = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\ResponseProviderInterface' );

        $this->mockForm = $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $this->mockResponse = $this->getMock( 'Symfony\\Component\\HttpFoundation\\Response' );

        $this->mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );

        $this->mockData = new \StdClass();
        $this->mockData->attribute = 'mock data';
    }

    public function testConstructorFormProviderInjection()
    {
        $facade = new DefaultFormFacade( $this->mockFormProvider, $this->mockHandlerProvider, $this->mockResponseProvider );

        $this->mockFormProvider->expects( $this->once() )
            ->method( 'getForm' )
            ->with( $this->identicalTo( $this->mockLocation ) )
            ->will( $this->returnValue( $this->mockForm ) );

        $form = $facade->getForm( $this->mockLocation );
        $this->assertEquals( $this->mockForm, $form );

        $this->mockHandlerProvider->expects( $this->once() )
            ->method( 'getHandler' )
            ->with( $this->identicalTo( $this->mockLocation ) )
            ->will( $this->returnValue( $this->mockHandler ) );

        $handler = $facade->getHandler( $this->mockLocation );
        $this->assertEquals( $this->mockHandler, $handler );

        $this->mockResponseProvider->expects( $this->once() )
            ->method( 'getResponse' )
            ->with( $this->identicalTo( $this->mockLocation ), $this->identicalTo( $this->mockData ) )
            ->will( $this->returnValue( $this->mockResponse ) );

        $response = $facade->getResponse( $this->mockLocation, $this->mockData );
        $this->assertEquals( $response, $this->mockResponse );
    }

    public function testDefaultFormProvider()
    {
        $handler = new DefaultFormFacade();
        $this->setExpectedException( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' );
        $handler->getForm( $this->mockLocation );
    }

    public function testDefaultHandlerProvider()
    {
        $handler = new DefaultFormFacade();
        $this->assertInstanceOf( 'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler', $handler->getHandler( $this->mockLocation ) );
    }

    public function testDefaultResponseProvider()
    {
        $handler = new DefaultFormFacade();
        $this->setExpectedException( 'Heliopsis\\eZFormsBundle\\Exceptions\\BadConfigurationException' );
        $handler->getResponse( $this->mockLocation, $this->mockData );
    }

    public function testSetFormProvider()
    {
        $facade = new DefaultFormFacade();
        $facade->setFormProvider( $this->mockFormProvider );

        $this->mockFormProvider->expects( $this->once() )
            ->method( 'getForm' )
            ->with( $this->identicalTo( $this->mockLocation ) )
            ->will( $this->returnValue( $this->mockForm ) );

        $form = $facade->getForm( $this->mockLocation );
        $this->assertEquals( $this->mockForm, $form );
    }

    public function testSetHandlerProvider()
    {
        $facade = new DefaultFormFacade();
        $facade->setHandlerProvider( $this->mockHandlerProvider );

        $this->mockHandlerProvider->expects( $this->once() )
            ->method( 'getHandler' )
            ->with( $this->identicalTo( $this->mockLocation ) )
            ->will( $this->returnValue( $this->mockHandler ) );

        $handler = $facade->getHandler( $this->mockLocation );
        $this->assertEquals( $this->mockHandler, $handler );
    }

    public function testSetResponseProvider()
    {
        $facade = new DefaultFormFacade();
        $facade->setResponseProvider( $this->mockResponseProvider );
        $this->mockResponseProvider->expects( $this->once() )
            ->method( 'getResponse' )
            ->with( $this->identicalTo( $this->mockLocation ), $this->identicalTo( $this->mockData ) )
            ->will( $this->returnValue( $this->mockResponse ) );

        $response = $facade->getResponse( $this->mockLocation, $this->mockData );
        $this->assertEquals( $response, $this->mockResponse );
    }
}
