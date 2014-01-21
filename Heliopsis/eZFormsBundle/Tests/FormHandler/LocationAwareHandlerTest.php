<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
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
