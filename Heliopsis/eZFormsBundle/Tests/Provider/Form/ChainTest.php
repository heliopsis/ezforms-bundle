<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Form;

use Heliopsis\eZFormsBundle\Provider\Form\Chain;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $mockProviders;

    /**
     * @var array
     */
    private $mockForms;

    /**
     * @var Location[]
     */
    private $locations;

    public function setUp()
    {
        $this->mockForms = array(
            $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )->disableOriginalConstructor()->getMock(),
        );

        $this->locations = array(
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
            $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' ),
        );
    }

    public function testChainLogicWithoutPriority()
    {
        $this->initProviders();

        $this->mockProviders[0]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[0] )
            ->will( $this->returnValue( $this->mockForms[0] ) );

        $this->mockProviders[0]->expects( $this->at( 1 ) )
            ->method( 'getForm' )
            ->with( $this->locations[1] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[0]->expects( $this->at( 2 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[1]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[1] )
            ->will( $this->returnValue( $this->mockForms[1] ) );

        $this->mockProviders[1]->expects( $this->at( 1 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[2]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->returnValue( $this->mockForms[2] ) );

        $chainProvider = new Chain();

        $chainProvider->addProvider( $this->mockProviders[0] );
        $chainProvider->addProvider( $this->mockProviders[1] );
        $chainProvider->addProvider( $this->mockProviders[2] );

        $this->assertSame(
            $this->mockForms[0],
            $chainProvider->getForm( $this->locations[0] )
        );

        $this->assertSame(
            $this->mockForms[1],
            $chainProvider->getForm( $this->locations[1] )
        );

        $this->assertSame(
            $this->mockForms[2],
            $chainProvider->getForm( $this->locations[2] )
        );
    }

    public function testChainLogicWithPriority()
    {
        $this->initProviders();

        $this->mockProviders[1]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[0] )
            ->will( $this->returnValue( $this->mockForms[0] ) );

        $this->mockProviders[1]->expects( $this->at( 1 ) )
            ->method( 'getForm' )
            ->with( $this->locations[1] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[1]->expects( $this->at( 2 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[0]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[1] )
            ->will( $this->returnValue( $this->mockForms[1] ) );

        $this->mockProviders[0]->expects( $this->at( 1 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->mockProviders[2]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[2] )
            ->will( $this->returnValue( $this->mockForms[2] ) );

        $chainProvider = new Chain();

        $chainProvider->addProvider( $this->mockProviders[0], 2 );
        $chainProvider->addProvider( $this->mockProviders[1], 3 );
        $chainProvider->addProvider( $this->mockProviders[2], 1 );

        $this->assertSame(
            $this->mockForms[0],
            $chainProvider->getForm( $this->locations[0] )
        );

        $this->assertSame(
            $this->mockForms[1],
            $chainProvider->getForm( $this->locations[1] )
        );

        $this->assertSame(
            $this->mockForms[2],
            $chainProvider->getForm( $this->locations[2] )
        );
    }

    public function testEmptyChainException()
    {
        $chainProvider = new Chain();

        $this->setExpectedException( 'Heliopsis\eZFormsBundle\Exceptions\UnknownFormException' );
        $chainProvider->getForm( $this->locations[0] );
    }

    public function testNotFindFormException()
    {
        $chainProvider = new Chain();

        $this->initProviders();

        $chainProvider->addProvider( $this->mockProviders[0] );

        $this->mockProviders[0]->expects( $this->at( 0 ) )
            ->method( 'getForm' )
            ->with( $this->locations[1] )
            ->will( $this->throwException( $this->getMock( 'Heliopsis\\eZFormsBundle\\Exceptions\\UnknownFormException' ) ) );

        $this->setExpectedException( 'Heliopsis\eZFormsBundle\Exceptions\UnknownFormException' );
        $chainProvider->getForm( $this->locations[0] );
    }

    private function initProviders()
    {
        $this->mockProviders   = array();
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\FormProviderInterface' );
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\FormProviderInterface' );
        $this->mockProviders[] = $this->getMock( 'Heliopsis\\eZFormsBundle\\Provider\\FormProviderInterface' );
    }
}
