<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Handler;

use Heliopsis\eZFormsBundle\Provider\Handler\ContentTypeIdentifierMap;

/**
 * Class ContentTypeIdentifierMapTest
 * @package Heliopsis\eZFormsBundle\Tests\Provider\Handler
 */
class ContentTypeIdentifierMapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    private $identifiers;

    public function testGetHandler()
    {
        $this->identifiers = array(
            'contentTypeIdentifier1',
            'contentTypeIdentifier2',
        );

        $provider = $this->getContentTypeIdentifierMap();
        $mockHandlers = array();

        foreach ( $this->identifiers as $id => $identifier )
        {
            $mockHandlers[$id] = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
            $provider->addFormHandler( $identifier, $mockHandlers[$id] );
        }

        $this->assertSame(
            $mockHandlers[0],
            $provider->getHandler( $this->getMockLocation( 0 ) )
        );

        $this->assertSame(
            $mockHandlers[1],
            $provider->getHandler( $this->getMockLocation( 1 ) )
        );
    }

    public function testHandlerIsNull()
    {
        $this->identifiers = array(
            'contentTypeIdentifier1',
        );

        $provider = $this->getContentTypeIdentifierMap();

        $this->assertInstanceOf(
            'Heliopsis\\eZFormsBundle\\FormHandler\\NullHandler',
            $provider->getHandler( $this->getMockLocation( 0 ) )
        );
    }

    private function getMockLocation( $contentTypeID )
    {
        $mockContentInfo = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\ContentInfo' );
        $mockContentInfo->expects( $this->any() )
            ->method( '__get' )
            ->with( 'contentTypeId' )
            ->will( $this->returnValue( $contentTypeID ) );

        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $mockLocation->expects( $this->any() )
            ->method( '__get' )
            ->with( 'contentInfo' )
            ->will( $this->returnValue( $mockContentInfo ) );

        return $mockLocation;
    }

    /**
     * @return ContentTypeIdentifierMap
     */
    private function getContentTypeIdentifierMap()
    {
        return new ContentTypeIdentifierMap( $this->getMockContentTypeService() );
    }

    private function getMockContentTypeService()
    {
        $mockContentTypeService = $this->getMock( 'eZ\\Publish\\API\\Repository\\ContentTypeService' );

        for ( $i = 0; $i < count( $this->identifiers ); $i++ )
        {
            $mockContentTypeService->expects( $this->at( $i ) )
                ->method( 'loadContentType' )
                ->with( $this->equalTo( $i ) )
                ->will( $this->returnValue( $this->getMockContentType( $this->identifiers[$i] ) ) );
        }

        return $mockContentTypeService;
    }

    private function getMockContentType( $contentTypeIdentifier )
    {
        $contentType = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\ContentType\\ContentType' );
        $contentType->expects( $this->any() )
            ->method( '__get' )
            ->with( 'identifier' )
            ->will( $this->returnValue( $contentTypeIdentifier ) );

        return $contentType;
    }

}
