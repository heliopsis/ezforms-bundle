<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider\Response;

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
