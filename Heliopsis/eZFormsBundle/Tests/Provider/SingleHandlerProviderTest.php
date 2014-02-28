<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\Provider;

use eZ\Publish\Core\Repository\Values\Content\Location;
use Heliopsis\eZFormsBundle\Provider\Handler\SingleHandlerProvider;

class SingleHandlerProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHandler()
    {
        $mockHandler = $this->getMock( 'Heliopsis\\eZFormsBundle\\FormHandler\\FormHandlerInterface' );
        $location = new Location();
        $provider = new SingleHandlerProvider( $mockHandler );
        $this->assertEquals( $mockHandler, $provider->getHandler( $location ) );
    }
}
