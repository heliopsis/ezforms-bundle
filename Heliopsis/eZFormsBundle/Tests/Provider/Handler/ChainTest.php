<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Handler;

use Heliopsis\eZFormsBundle\Provider\Handler\Chain;

/**
 * Class ChainTest
 * @package Heliopsis\eZFormsBundle\Tests\Provider\Handler
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $mockProviders;

    /**
     * @var array
     */
    private $mockHandlers;

    /**
     * @var Location[]
     */
    private $locations;

    private $nullHandler;

    public function setUp()
    {
        $this->mockHandlers = array(
            $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' ),
            $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' ),
            $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' ),
        );

        $this->locations = array(
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
        );

        $this->nullHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler' );
    }

    public function testChainLogicWithoutPriority()
    {
        $this->initProviders();

        $this->mockProviders[0]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[0], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[0] ) );

        $this->mockProviders[0]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[1] )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[0]->expects( $this->at( 2 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[0]->expects( $this->at( 3 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[1]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[1], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[1] ) );

        $this->mockProviders[1]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[1]->expects( $this->at( 2 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[2]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[2] ) );

        $this->mockProviders[2]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $chainProvider = new Chain();

        $chainProvider->addProvider( $this->mockProviders[0] );
        $chainProvider->addProvider( $this->mockProviders[1] );
        $chainProvider->addProvider( $this->mockProviders[2] );

        $this->assertSame(
            $this->mockHandlers[0],
            $chainProvider->getHandler( $this->locations[0], 'viewType' )
        );

        $this->assertSame(
            $this->mockHandlers[1],
            $chainProvider->getHandler( $this->locations[1], 'viewType' )
        );

        $this->assertSame(
            $this->mockHandlers[2],
            $chainProvider->getHandler( $this->locations[2], 'viewType' )
        );

        $this->assertInstanceOf(
            'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler',
            $chainProvider->getHandler(  $this->locations[3], 'viewType' )
        );
    }

    public function testChainLogicWithPriority()
    {
        $this->initProviders();

        $this->mockProviders[1]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[0], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[0] ) );

        $this->mockProviders[1]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[1], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[1]->expects( $this->at( 2 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[1]->expects( $this->at( 3 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[0]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[1], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[1] ) );

        $this->mockProviders[0]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[0]->expects( $this->at( 2 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $this->mockProviders[2]->expects( $this->at( 0 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[2], 'viewType' )
            ->will( $this->returnValue( $this->mockHandlers[2] ) );

        $this->mockProviders[2]->expects( $this->at( 1 ) )
            ->method( 'getHandler' )
            ->with( $this->locations[3], 'viewType' )
            ->will( $this->returnValue( $this->nullHandler ) );

        $chainProvider = new Chain();

        $chainProvider->addProvider( $this->mockProviders[0], 2 );
        $chainProvider->addProvider( $this->mockProviders[1], 3 );
        $chainProvider->addProvider( $this->mockProviders[2], 1 );

        $this->assertSame(
            $this->mockHandlers[0],
            $chainProvider->getHandler( $this->locations[0], 'viewType' )
        );

        $this->assertSame(
            $this->mockHandlers[1],
            $chainProvider->getHandler( $this->locations[1], 'viewType' )
        );

        $this->assertSame(
            $this->mockHandlers[2],
            $chainProvider->getHandler( $this->locations[2], 'viewType' )
        );

        $this->assertInstanceOf(
            'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler',
            $chainProvider->getHandler(  $this->locations[3], 'viewType' )
        );
    }

    private function initProviders()
    {
        $this->mockProviders   = array();
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\HandlerProviderInterface' );
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\HandlerProviderInterface' );
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\HandlerProviderInterface' );
    }
}
