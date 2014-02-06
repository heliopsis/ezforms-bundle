<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\FormHandler;

class LocationAwareHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetLocation()
    {
        $mockLocation = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Location' );
        $handler = $this->getMockForAbstractClass( 'Heliopsis\\eZFormsBundle\\FormHandler\\LocationAwareHandler' );

        $handler->setLocation( $mockLocation );
        $this->assertSame( $mockLocation, $handler->getLocation() );
    }
}
