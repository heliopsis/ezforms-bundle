ezforms-bundle
==============

This bundle provides a flexible way to associate Symfony forms to eZPublish contents.

Features:

- Form controller extending eZPublish's view controller
- Facade pattern for flexible form handling
- Separate interfaces for form instanciation, data handling and response generation
- Abtract classes for content related data handling
- Unit tests

*NB:* this bundle does not provide out of the box forms in eZPublish, it rather gives you tools to easily define
custom forms and leverage eZPublish's content tree to access or configure those forms.


License
-------

    TODO


Installation
------------

### 1. Download bundle using composer

```js
{
    require: {
        "heliopsis/ezforms-bundle": "1.0.*"
    }
}
```

### 2. Enable bundle in

``` php
<?php
// ezpublish/EzPublishKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Heliopsis\eZFormsBundle\HeliopsiseZFormsBundle(),
    );
}
```

### 3. Configure providers

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  providers:
    form: acme_forms.custom_form_provider
    handler: acme_forms.custom_handler_provider
```

*NB:* see Usage section for service definitions


### 4. Use form controller to render your locations views

```yaml
# ezpublish/config/ezpublish.yml

ezpublish:
  system:
    frontend_group:
      location_view:
        full:
          form:
            controller: heliopsis_ezforms.controller:formAction
            template: AcmeDesignBundle:full:form.html.twig
            match:
              Identifier\ContentType: 'form'
        confirm:
          template: AcmeDesignBundle:confirm:form.html.twig
          match:
            Identifier\ContentType: 'form'
```


Basic Usage
-----------

This bundle comes with a default facade that delegates 3 things to 3 different services:

- *Form provider:* knows which form to display at the right location
- *Handler provider:* knows which service should handle user data once form is validated
- *Response provider:* knows where user should be redirected once data has been handled

Out of the box, a default response provider is used, redirecting to the `confirm` view of current location.
However, you must create form and handler providers suiting your custom needs.


### Form provider

This service is in charge of instantiating the form to be displayed at current location.
It can be as evolved as a form factory reading form definition from content fields
or as simple as a remoteId to form type mapping. It only has to implement `FormProviderInterface`:

```php
<?php
//Acme/FormsBundle/Providers/FormProvider.php

namespace Acme\FormsBundle\Providers;

use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Symfony\Component\Form\FormFactoryInterface;
use eZ\Publish\API\Repository\Values\Content\Location;

class FormProvider implements FormProviderInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException si aucun formulaire ne correspond
     */
    public function getForm( Location $location )
    {
        switch ( $location->contentInfo->remoteId )
        {
            case 'feedback_form':
                return $this->formFactory->create( 'acme.forms.type.feedback' );

            case 'newsletter_subscription':
                return $this->formFactory->create( 'acme.forms.type.newsletter' );
        }

        throw new UnknownFormException( sprintf( "No form is defined for remoteId %s", $location->contentInfo->remoteId ) );
    }
}
```

You then need to define your provider as a service:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

parameters:
    acme_forms.custom_form_provider.class: Acme\FormsBundle\Providers\FormProvider

services:
  acme_forms.custom_form_provider:
  class: %acme_forms.custom_form_provider.class%
  arguments: [@form.factory]

```

... and tell ezforms-bundle to use it as your default form provider:

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  providers:
    form: acme_forms.custom_form_provider
```


### Handler provider

This service is in charge of instanciating the data handling services. You may either define a single type of handling
for the whole site or use different strategies according to current location.
For example, you could email your feedback form data to site administrator but only forward newsletter subscriptions to
the MailChimp API.

Once again an interface is the only limitation here:

```php
<?php
//Acme/FormsBundle/Providers/HandlerProvider.php

namespace Acme\FormsBundle\Providers;

use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Heliopsis\eZFormsBundle\FormHandler\LocationAwareHandlerInterface
use eZ\Publish\API\Repository\Values\Content\Location;

class HandlerProvider implements HandlerProviderInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;

    /**
     * @param FormFactoryInterface $formFactory
     */
    function __construct(FormFactoryInterface $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        if ( $this->formHandler instanceof LocationAwareHandlerInterface )
        {
            $this->formHandler->setLocation( $location );
        }

        return $this->formHandler;
    }
}
```

You then need to define your provider as a service:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

parameters:
    acme_forms.custom_handler_provider.class: Acme\FormsBundle\Providers\HandlerProvider

services:
  acme_forms.custom_handler_provider:
  class: %acme_forms.custom_handler_provider.class%
  arguments: [@acme_forms.handlers.logger]

```

... and tell ezforms-bundle to use it as your default form provider:

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  providers:
    handler: acme_forms.custom_handler_provider
```


### Form handler(s)

You may define as many form handlers as you want, they may do anything you wish with forms data from emailing it
to storing it in a database. They simply get the data passed, this bundles does not care what you do with it.

```php
<?php
//Acme/FormsBundle/Handlers/EmailHandler.php

namespace Acme\FormsBundle\FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use Swift_Mailer;
use \Twig_Environment;

class CustomFormHandler implements FormHandlerInterface
{
    /**
     * @var string
     */
    private $recipient;

    /**
     * @var string
     */
    private $template;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * @var \Swift_Mailer $mailer
     * @var
     * @var string $template
     * @var string $recipient
     */
    public function __construct( Swift_Mailer $mailer, Twig_Environment $templating, $template, $recipient )
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->template = $template;
        $this->recipient = $recipient;
    }

    /**
     * Does whatever needs to be done with $data
     * @param mixed $data
     * @return void
     */
    public function handle( $data )
    {
        $body = $this->templating->render(
            $this->template,
            array(
                'data' => $data
            )
        );
        $message = \Swift_Message::newInstance();
        $message->setSubject("Sent Data");
        $message->setTo($this->recipient)
        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}
```

This bundle does provide any implementation but only a few utility classes and interfaces:

#### ChainHandler

Use this class if you want to combine several handlers (i.e. store in DB and send an email to both user and administrator):

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.default_handler:
  class: %heliopsis_ezforms.handlers.chain.class%
  arguments: [@ezpublish.api.service.content]
  calls:
    - [ addHandler, [ @acme_forms.handlers.db ] ]
    - [ addHandler, [ @acme_forms.handlers.user_email ] ]
    - [ addHandler, [ @acme_forms.handlers.admin_email ] ]

```

#### LocationAwareHandler and ContentAwareHandler

These handler types (interfaces and abstract classes are available) allow you to use eZPublish content as context
when handling data (i.e. specify administrator email in a content field or indicate location URL somewhere).

If your handler implements one of these interfaces, ChainHandler and DefaultFacade will inject location or content
before handling data;

### Views



Advanced Usage
--------------

### Response provider


### Custom facade