<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\ChainHandler;

class ChainHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var mixed
     */
    private $mockData;

    protected function setUp()
    {
        $this->mockData = new \StdClass();
        $this->mockData->attribute = 'mock data';
    }

    /**
     * @param array $subHandlers
     * @return ChainHandler
     */
    private function getHandler( array $subHandlers = array() )
    {
        return new ChainHandler( $subHandlers );
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
        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );

        $mockLocationAwareHandler->expects( $this->once() )
            ->method( 'setLocation' )
            ->with( $this->identicalTo( $mockLocation ) );

        $handler = $this->getHandler( array( $mockLocationAwareHandler ) );
        $handler->setLocation( $mockLocation );
    }

    public function testSetContent()
    {
        $mockContentAwareHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\ContentAwareHandlerInterface' );
        $mockContent = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Content' );

        $mockContentAwareHandler->expects( $this->once() )
            ->method( 'setContent' )
            ->with( $this->identicalTo( $mockContent ) );

        $handler = $this->getHandler( array( $mockContentAwareHandler ) );
        $handler->setContent( $mockContent );

    }
}
