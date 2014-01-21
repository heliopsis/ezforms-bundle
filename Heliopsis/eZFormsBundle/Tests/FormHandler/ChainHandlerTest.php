<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Tests\FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\ChainHandler;

class ChainHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContentService;

    /**
     * @var mixed
     */
    private $mockData;

    protected function setUp()
    {
        $this->mockContentService = $this->getMock( 'eZ\\Publish\\API\\Repository\\ContentService' );

        $this->mockData = new \StdClass();
        $this->mockData->attribute = 'mock data';
    }

    /**
     * @param array $subHandlers
     * @return ChainHandler
     */
    private function getHandler( array $subHandlers = array() )
    {
        return new ChainHandler( $this->mockContentService, $subHandlers );
    }

    public function testHandle()
    {
        $handler1 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $handler2 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $handler3 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );

        $handler = $this->getHandler( array( $handler1, $handler2, $handler3 ) );
        $handler1->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler2->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler3->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler->handle( $this->mockData );
    }

    public function testAddHandler()
    {
        $handler1 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $handler2 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $handler3 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );

        $handler = $this->getHandler( array( $handler1, $handler2 ) );
        $handler->addHandler( $handler3 );

        $handler1->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler2->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler3->expects( $this->once() )
            ->method( 'handle' )
            ->with( $this->identicalTo( $this->mockData ) );

        $handler->handle( $this->mockData );
    }

    public function testSetLocation()
    {
        $mockLocationAwareHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\LocationAwareHandlerInterface' );
        $mockContentAwareHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\ContentAwareHandlerInterface' );

        $mockContentInfo = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\ContentInfo' );
        $mockContent = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Content' );

        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $mockLocation->expects( $this->once() )
            ->method( '__get' )
            ->with( 'contentInfo' )
            ->will( $this->returnValue( $mockContentInfo ) );

        $this->mockContentService->expects( $this->once() )
            ->method( 'loadContentByContentInfo' )
            ->with( $mockContentInfo )
            ->will( $this->returnValue( $mockContent ) );

        $mockLocationAwareHandler->expects( $this->once() )
            ->method( 'setLocation' )
            ->with( $this->identicalTo( $mockLocation ) );

        $mockContentAwareHandler->expects( $this->once() )
            ->method( 'setContent' )
            ->with( $this->identicalTo( $mockContent ) );

        $handler = $this->getHandler( array( $mockLocationAwareHandler, $mockContentAwareHandler ) );
        $handler->setLocation( $mockLocation );
    }
}
