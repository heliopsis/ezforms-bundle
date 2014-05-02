<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\FormHandler\SwiftMailer;

use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractHandler implements FormHandlerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Template engine
     * @var EngineInterface
     */
    private $templating;

    /**
     * Translator
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Mail template
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $contentType = 'text/plain';

    /**
     * @var string
     */
    private $subject;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param \Swift_Mailer $mailer
     * @param $templating
     * @param $translator
     */
    public function __construct( \Swift_Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator = null )
    {
        $this->mailer  = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * Set the template path used for mail
     * @param $template
     */
    public function setTemplate( $template )
    {
        $this->template = $template;
    }

    /**
     * @param string $contentType
     * @return void
     */
    public function setContentType( $contentType )
    {
        $this->contentType = $contentType;
    }

    /**
     * Set the subject of the mail
     * @param $subject
     */
    public function setSubject( $subject )
    {
        $translator = $this->getTranslator();
        $this->subject = $translator ? $translator->trans( $subject ) : $subject;
    }

    /**
     * @return mixed
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return \Swift_Mailer
     */
    protected function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @return EngineInterface
     */
    protected function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Exécute le traitement
     * @param mixed $data
     * @return void
     */
    public function handle( $data )
    {
        $this->data = $data;

        if ( $message = $this->createMessage() )
        {
            $this->getMailer()->send($message);
        }
    }

    /**
     * @return \Swift_Message
     */
    protected function createMessage()
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject( $this->getSubject() );
        $message->setContentType( $this->getContentType() );
        $message->setBody( $this->renderBody() );

        $this->addSender( $message );
        $this->addRecipients( $message );
        $this->addAttachments( $message );

        return $message;
    }

    /**
     * @return string
     */
    protected function renderBody()
    {
        $params = $this->getTemplateParameters() + array( 'data' => $this->getData() );
        return $this->getTemplating()->render(
            $this->getTemplate(),
            $params
        );
    }

    /**
     * @return array
     */
    abstract protected function getTemplateParameters();

    /**
     * @param \Swift_Message $message
     */
    abstract protected function addAttachments( \Swift_Message $message );

    /**
     * @return void
     */
    abstract protected function addRecipients( \Swift_Message $message );

    /**
     * @return mixed
     */
    abstract protected function addSender( \Swift_Message $message );

}
