<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @author: Broussolle Brice <brice.broussolle@outlook.com>
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Form;

use Heliopsis\eZFormsBundle\Provider\Form\ContentTypeIdentifierMap;

/**
 * Class ContentTypeIdentifierMapTest
 * @package Heliopsis\eZFormsBundle\Tests\Provider\Form
 */
class ContentTypeIdentifierMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormFactory;

    /**
     * @var Array
     */
    private $identifiers;

    public function setUp()
    {
        $this->mockFormFactory = $this->getMock( 'Symfony\\Component\\Form\\FormFactoryInterface' );
    }

    public function testGetForm()
    {
        $this->identifiers = array(
            'contentTypeIdentifier1',
            'contentTypeIdentifier2',
        );

        $provider = $this->getContentTypeIdentifierMap();
        $mockForms = array();

        foreach ( $this->identifiers as $id => $identifier )
        {
            $provider->addFormType( $identifier, 'formType_' . $id );
            $mockForms[] = $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )
                ->disableOriginalConstructor()
                ->getMock();
            $this->mockFormFactory->expects( $this->at( $id ) )
                ->method( 'create' )
                ->with( 'formType_' . $id )
                ->will( $this->returnValue( $mockForms[$id] ) );
        }

        $this->assertSame(
            $mockForms[0],
            $provider->getForm( $this->getMockLocation( 0 ), 'viewType' )
        );

        $this->assertSame(
            $mockForms[1],
            $provider->getForm( $this->getMockLocation( 1 ), 'viewType' )
        );
    }

    public function testGetFormThrowsException()
    {
        $this->identifiers = array(
            'contentTypeIdentifier1',
        );

        $provider = $this->getContentTypeIdentifierMap();

        $this->setExpectedException( 'Heliopsis\eZFormsBundle\Exceptions\UnknownFormException' );
        $provider->getForm( $this->getMockLocation( 0 ), 'viewType' );
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
        return new ContentTypeIdentifierMap( $this->mockFormFactory, $this->getMockContentTypeService() );
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
