<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider;

use Heliopsis\eZFormsBundle\Provider\Handler\SingleHandlerProvider;

class SingleHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contentServiceMock;

    function __construct()
    {
        $this->contentServiceMock = $this->getMock( 'eZ\\Publish\\API\\Repository\\ContentService' );
    }

    public function testGetHandler()
    {
        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $provider = new SingleHandlerProvider( $mockHandler, $this->contentServiceMock );
        $this->assertEquals( $mockHandler, $provider->getHandler( $mockLocation ) );
    }

    public function testGetLocationAwareHandler()
    {
        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\LocationAwareHandlerInterface' );
        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $provider = new SingleHandlerProvider( $mockHandler, $this->contentServiceMock );

        $mockHandler->expects( $this->once() )
            ->method( 'setLocation' )
            ->with( $mockLocation );

        $this->assertEquals( $mockHandler, $provider->getHandler( $mockLocation ) );
    }

    public function testGetContentAwareHandler()
    {
        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\ContentAwareHandlerInterface' );
        $mockContentInfo = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\ContentInfo' );
        $mockContent = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Content' );
        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );

        $provider = new SingleHandlerProvider( $mockHandler, $this->contentServiceMock );

        $mockLocation->expects( $this->once() )
            ->method( '__get' )
            ->with( 'contentInfo' )
            ->will( $this->returnValue( $mockContentInfo ) );

        $this->contentServiceMock->expects( $this->once() )
            ->method( 'loadContentByContentInfo' )
            ->with( $mockContentInfo )
            ->will(
                $this->returnValue( $mockContent )
            );

        $mockHandler->expects( $this->once() )
            ->method( 'setContent' )
            ->with( $mockContent );

        $this->assertEquals( $mockHandler, $provider->getHandler( $mockLocation ) );
    }
}
