<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
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
