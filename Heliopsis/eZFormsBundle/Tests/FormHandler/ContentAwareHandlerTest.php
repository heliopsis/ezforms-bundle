<?php
/**
 * @author: Benjamin Choquet <bchoquet@heliopsis.net>
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @licence: proprietary
 */

namespace Heliopsis\eZFormsBundle\Tests\FormHandler;

class ContentAwareHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetContent()
    {
        $mockContent = $this->getMock( 'eZ\\Publish\\API\\Repository\\Values\\Content\\Content' );
        $handler = $this->getMockForAbstractClass( 'Heliopsis\\eZFormsBundle\\FormHandler\\ContentAwareHandler' );

        $handler->setContent( $mockContent );
        $this->assertSame( $mockContent, $handler->getContent() );
    }
}
