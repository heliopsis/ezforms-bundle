<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Form;

use Heliopsis\eZFormsBundle\Provider\Form\ContentRemoteIdMap;

class ContentRemoteIdMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFormFactory;

    /**
     * @var \Heliopsis\eZFormsBundle\Provider\Form\ContentRemoteIdMap
     */
    private $provider;

    public function setUp()
    {
        $this->mockFormFactory = $this->getMock( 'Symfony\\Component\\Form\\FormFactoryInterface' );

        $this->provider = new ContentRemoteIdMap( $this->mockFormFactory );
        $this->provider->addFormType( 'remoteId1', 'formType1' );
        $this->provider->addFormType( 'remoteId2', 'formType2' );

    }

    public function testGetForm()
    {
        $mockForm1 = $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )
            ->disableOriginalConstructor()
            ->getMock();

        $mockForm2 = $this->getMockBuilder( 'Symfony\\Component\\Form\\Form' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockFormFactory->expects( $this->at( 0 ) )
            ->method( 'create' )
            ->with( 'formType1' )
            ->will( $this->returnValue( $mockForm1 ) );

        $this->mockFormFactory->expects( $this->at( 1 ) )
            ->method( 'create' )
            ->with( 'formType2' )
            ->will( $this->returnValue( $mockForm2 ) );

        $this->assertSame(
            $mockForm1,
            $this->provider->getForm( $this->getMockLocation( 'remoteId1' ) )
        );

        $this->assertSame(
            $mockForm2,
            $this->provider->getForm( $this->getMockLocation( 'remoteId2' ) )
        );
    }

    public function testGetFormThrowsException()
    {
        $this->setExpectedException( 'Heliopsis\eZFormsBundle\Exceptions\UnknownFormException' );
        $this->provider->getForm( $this->getMockLocation( 'notMapped' ) );
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
