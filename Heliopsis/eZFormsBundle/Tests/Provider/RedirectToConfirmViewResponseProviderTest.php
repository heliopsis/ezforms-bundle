<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider;


use eZ\Publish\Core\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Provider\Response\RedirectToConfirmViewResponseProvider;

class RedirectToConfirmViewResponseProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockUrlGenerator;

    /**
     * @var string
     */
    protected $mockViewType;

    /**
     * @var RedirectToConfirmViewResponseProvider
     */
    protected $provider;

    protected function setUp()
    {
        $this->mockUrlGenerator = $this->getMock( 'Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface' );
        $this->mockViewType = 'mockViewType';
        $this->provider = new RedirectToConfirmViewResponseProvider( $this->mockUrlGenerator, $this->mockViewType );
    }


    public function testGetResponse()
    {
        $location = new Location( array( 'id' => 17 ) );
        $this->mockUrlGenerator->expects( $this->once() )
            ->method( 'generate' )
            ->with(
                '_ezpublishLocation',
                array(
                    'locationId' => $location->id,
                    'viewType' => $this->mockViewType,
                )
            )
            ->will(
                $this->returnValue( '/mock/url' )
            );

        $response = $this->provider->getResponse( $location, null );
        $this->assertInstanceOf( 'Symfony\\Component\\HttpFoundation\\RedirectResponse', $response );
        $this->assertEquals( '/mock/url', $response->headers->get( 'location' ) );
    }

}
