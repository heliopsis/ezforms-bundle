# Form handlers

You may define as many form handlers as you want, they may do anything you wish with forms data from emailing it
to storing it in a database. They simply get the data passed, this bundles does not care what you do with it.

```php
<?php
//Acme/FormsBundle/Handlers/DoctrinePersistenceHandler.php

namespace Acme\FormsBundle\FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePersistenceHandler implements FormHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager

    /**
     * @var EntityManagerInterface $entityManager
     */
    public function __construct( EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Stores data in database
     * @param mixed $data
     * @return void
     * @todo check that $data is a doctrine entity
     */
    public function handle( $data )
    {
        $this->entityManager->persist( $data );
        $this->entityManager->flush();
    }
}
```

## Available Handlers

This bundle does not provide any complete implementation but only a few utility classes and interfaces:

### ChainHandler

Use this class if you want to combine several handlers (i.e. store in DB and send an email to both user and administrator):

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.default_handler:
  class: %heliopsis_ezforms.handlers.chain.class%
  calls:
    - [ addHandler, [ @acme_forms.handlers.db ] ]
    - [ addHandler, [ @acme_forms.handlers.user_email ] ]
    - [ addHandler, [ @acme_forms.handlers.admin_email ] ]

```


### SwiftMailer Abstract handler

This abstract class can be used to ease the process of sending out emails. You still have to create your own service
and implement 4 abstract methods to define who sends what to whom :

```php
<?php
// Acme/FormsBundle/Handlers/AdminEmailHandler.php

namespace Acme/FormsBundle/Handlers/FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\SwiftMailer\AbstractHandler;
use Acme\FormsBundle\Model\Feedback;

class AdminEmailHandler extends AbstractHandler
{
    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * @var string
     */
    private $recipientName;

    /**
     * @param $email
     * @param string $name
     */
    public function setRecipient( $email, $name = '' )
    {
        $this->recipientEmail = $email;
        $this->recipientName = $name;
    }

    /**
     * @return void
     */
    protected function addRecipients( \Swift_Message $message )
    {
        if ( !$this->recipientEmail )
        {
            throw new \RuntimeException( "Recipient unknown" );
        }

        $message->addTo( $this->recipientEmail, $this->recipientName );
    }

    /**
     * @return mixed
     */
    protected function addSender( \Swift_Message $message )
    {
        $data = $this->getData();
        if ( !$data instanceof Feedback )
        {
            throw new \RuntimeException( "Data should be of the feedback type" );
        }

        $message->addFrom( $data->email, $data->fullName );
    }

    /**
     * @return array
     */
    protected function getTemplateParameters()
    {
        return array(
            'subject' => $this->getSubject(),
        );
    }

    /**
     * @param \Swift_Message $message
     */
    protected function addAttachments( \Swift_Message $message )
    {
    }
}
```

Your service now only needs to know which template should be rendered, the recipient email address and the subject:

```yml
# src/Acme/FormsBundle/Resources/config/services.yml

parameters:
  acme_forms.handlers.admin_email.class: Acme\FormsBundle\Handlers\AdminEmailHandler

services:
  acme_forms.handlers.admin_email:
    class: %acme_forms.handlers.admin_email.class%
    arguments: [ @swiftmailer.mailer, @templating, @translator ]
    calls:
      - [ setContentType, [ 'text/html' ] ]
      - [ setTemplate, [ 'AcmeFormsBundle:email:feedback.html.twig' ] ]
      - [ setSubject, [ 'New feedback from the website' ] ]
      - [ setRecipient, [ %admin.email%, %admin.name% ] ]
```

Your template will get passed a `data` parameter for you to display submitted data in the email:

```twig
{# src/Acme/AcmeFormsBundle/Resources/views/email/feedback.html.twig #}
<!DOCTYPE html>
<html>
    <head>
        <title>{{ subject }}</title>
    </head>
    <body>
        <h1>{{ subject }}</h1>
        <p>{{ data.fullName }} ({{ data.email }} ) submitted this message:</p>

        <p>{{ data.message|nl2br }}</p>
    </body>
</html>
```

### LocationAwareHandler and ContentAwareHandler

These handler types (interfaces and abstract classes are available) allow you to use eZPublish content as context
when handling data (i.e. specify administrator email in a content field or indicate location URL somewhere).

If your handler implements one of these interfaces, FormController will inject location or content before handling data.

For example if you need to use content related data in your handler class, you could modify your `AdminEmailHandler`
to load the recipient email address from your form content:


```php
<?php
// Acme/FormsBundle/Handlers/AdminEmailHandler.php

namespace Acme/FormsBundle/Handlers/FormHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use Heliopsis\eZFormsBundle\FormHandler\SwiftMailer\AbstractHandler;
use Heliopsis\eZFormsBundle\FormHandler\ContentAwareHandlerInterface;
use Acme\FormsBundle\Model\Feedback;

class AdminEmailHandler extends AbstractHandler implements ContentAwareHandlerInterface
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @param Content $content
     * @return void
     */
    public function setContent( Content $content )
    {
        $this->content = $content;
    }

    /**
     * @return void
     */
    protected function addRecipients( \Swift_Message $message )
    {
        $recipientFieldValue = $this->content->getFieldValue( 'recipient' );
        if ( !$recipientFieldValue instanceof EmailAddressValue || !$recipientFieldValue->email )
        {
            throw new \Exception();
        }

        $message->setTo( $recipientFieldValue->email );
    }

    /**
     * @return mixed
     */
    protected function addSender( \Swift_Message $message )
    {
        $data = $this->getData();
        if ( !$data instanceof Feedback )
        {
            throw new \RuntimeException( "Data should be of the feedback type" );
        }

        $message->addFrom( $data->email, $data->fullName );
    }

    /**
     * @return array
     */
    protected function getTemplateParameters()
    {
        return array(
            'subject' => $this->getSubject(),
        );
    }

    /**
     * @param \Swift_Message $message
     */
    protected function addAttachments( \Swift_Message $message )
    {
    }
}
```
