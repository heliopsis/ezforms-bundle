<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\Tests\FormHandler\SwiftMailer;

use Heliopsis\eZFormsBundle\FormHandler\SwiftMailer\AbstractHandler;

class AbstractHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMailer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockTemplateEngine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockTranslator;

    /**
     * @var \Heliopsis\eZFormsBundle\FormHandler\SwiftMailer\AbstractHandler
     */
    private $handler;

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @var array
     */
    private $templateParams = array(
        'param1' => 'value1',
        'param2' => 'value2',
    );

    public function setUp()
    {
        $this->mockMailer = $this->getMockBuilder( 'Swift_Mailer' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTemplateEngine = $this->getMockBuilder( 'Symfony\\Component\\Templating\\EngineInterface' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockTemplateEngine->expects( $this->any() )
            ->method( 'render' )
            ->will( $this->returnValue( 'rendered template' ) );

        $this->mockTranslator = $this->getMockBuilder( 'Symfony\\Component\\Translation\\TranslatorInterface' )
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = $this->getMockForAbstractClass(
            'Heliopsis\\eZFormsBundle\\FormHandler\\SwiftMailer\\AbstractHandler',
            array( $this->mockMailer, $this->mockTemplateEngine, $this->mockTranslator )
        );

        $this->handler->expects( $this->any() )
            ->method( 'getTemplateParameters' )
            ->will( $this->returnValue( $this->templateParams ) );

        $this->data = new \StdClass();
        $this->data->field = 'value';
    }

    public function testSendsMail()
    {
        $this->mockMailer->expects( $this->once() )
            ->method( 'send' );
        $this->handler->handle( $this->data );
    }

    public function testSetTemplate()
    {
        $this->handler->setTemplate( 'ArbitraryTemplateIdentifier' );
        $this->assertEquals( 'ArbitraryTemplateIdentifier', $this->handler->getTemplate() );

        $this->mockTemplateEngine->expects( $this->once() )
            ->method( 'render' )
            ->with( 'ArbitraryTemplateIdentifier' );

        $this->handler->handle( $this->data );
    }

    public function testSetContentType()
    {
        $this->handler->setContentType( 'text/dummy' );
        $this->assertEquals( 'text/dummy', $this->handler->getContentType() );

        $this->mockMailer->expects( $this->once() )
            ->method( 'send' )
            ->with(
                $this->callback(
                    function( $o )
                    {
                        return $o instanceof \Swift_Message && $o->getContentType() === 'text/dummy';
                    }
                )
            );

        $this->handler->handle( $this->data );
    }

    public function testSetSubject()
    {
        $this->mockTranslator->expects( $this->once() )
            ->method( 'trans' )
            ->with( 'Test subject' )
            ->will( $this->returnValue( 'Translated test subject' ) );

        $this->handler->setSubject( 'Test subject' );
        $this->assertEquals( 'Translated test subject', $this->handler->getSubject() );

        $this->mockMailer->expects( $this->once() )
            ->method( 'send' )
            ->with(
                $this->callback(
                    function( $o )
                    {
                        return $o instanceof \Swift_Message && $o->getSubject() === 'Translated test subject';
                    }
                )
            );

        $this->handler->handle( $this->data );
    }

    public function testSubjectWithoutTranslator()
    {
        $handler = $this->getMockForAbstractClass(
            'Heliopsis\\eZFormsBundle\\FormHandler\\SwiftMailer\\AbstractHandler',
            array( $this->mockMailer, $this->mockTemplateEngine )
        );

        $handler->expects( $this->any() )
            ->method( 'getTemplateParameters' )
            ->will( $this->returnValue( $this->templateParams ) );

        $handler->setSubject( 'Test subject' );
        $this->assertEquals( 'Test subject', $handler->getSubject() );

        $this->mockMailer->expects( $this->once() )
            ->method( 'send' )
            ->with(
                $this->callback(
                    function( $o )
                    {
                        return $o instanceof \Swift_Message && $o->getSubject() === 'Test subject';
                    }
                )
            );

        $handler->handle( $this->data );
    }

    public function testAbstractMethodsCalled()
    {
        $messageInstanceConstraintCallback = function( $o ){
            return $o instanceof \Swift_Message;
        };

        $this->handler->expects( $this->once() )
            ->method( 'getTemplateParameters' );

        $this->handler->expects( $this->once() )
            ->method( 'addAttachments' )
            ->with( $this->callback( $messageInstanceConstraintCallback ) );

        $this->handler->expects( $this->once() )
            ->method( 'addRecipients' )
            ->with( $this->callback( $messageInstanceConstraintCallback ) );

        $this->handler->expects( $this->once() )
            ->method( 'addSender' )
            ->with( $this->callback( $messageInstanceConstraintCallback ) );

        $this->handler->handle( $this->data );
    }

    public function testDataTransmittedToTemplateEngine()
    {
        $expectedData = $this->data;
        $this->mockTemplateEngine->expects( $this->once() )
            ->method( 'render' )
            ->with(
                $this->anything(),
                $this->callback(
                    function( $o ) use ( $expectedData )
                    {
                        return is_array( $o ) && isset( $o['data'] ) && $o['data'] === $expectedData;
                    }
                )
            );

        $this->handler->handle( $this->data );
    }
}
