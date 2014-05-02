<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Handler;

use Heliopsis\eZFormsBundle\Provider\Handler\ContentRemoteIdMap;

class ContentRemoteIdMapTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHandler()
    {
        $mockHandler1 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $mockHandler2 = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );

        $provider = new ContentRemoteIdMap();
        $provider->addFormHandler( 'remoteId1', $mockHandler1 );
        $provider->addFormHandler( 'remoteId2', $mockHandler2 );

        $this->assertSame(
            $mockHandler1,
            $provider->getHandler( $this->getMockLocation( 'remoteId1' ) )
        );

        $this->assertSame(
            $mockHandler2,
            $provider->getHandler( $this->getMockLocation( 'remoteId2' ) )
        );

        $this->assertInstanceOf(
            'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler',
            $provider->getHandler( $this->getMockLocation( 'notMapped' ) )
        );
    }

    private function getMockLocation( $contentRemoteId )
    {
        $mockContentInfo = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\ContentInfo' );
        $mockContentInfo->expects( $this->any() )
            ->method( '__get' )
            ->with( 'remoteId' )
            ->will( $this->returnValue( $contentRemoteId ) );

        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $mockLocation->expects( $this->any() )
            ->method( '__get' )
            ->with( 'contentInfo' )
            ->will( $this->returnValue( $mockContentInfo ) );

        return $mockLocation;
    }
}
