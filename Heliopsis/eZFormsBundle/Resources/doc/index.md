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
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException
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

class EmailHandler implements FormHandlerInterface
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
        $message->setSubject("Data sent");
        $message->setTo($this->recipient)
        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}
```

This bundle does not provide any implementation but only a few utility classes and interfaces:

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

If your handler implements one of these interfaces, ChainHandler, DefaultFacade and SingleHandlerProvider will inject
location or content before handling data.

For example if you need to use content related data in your handler class, you could modify your `EmailHandler`
like this :

```php
<?php
//Acme/FormsBundle/Handlers/EmailHandler.php

namespace Acme\FormsBundle\FormHandler;

use Heliopsis\eZFormsBundle\FormHandler\ContentAwareHandler;
use Swift_Mailer;
use \Twig_Environment;

class EmailHandler extends ContentAwareHandler
{
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
    public function __construct( Swift_Mailer $mailer, Twig_Environment $templating, $template )
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->template = $template;
    }

    /**
     * Does whatever needs to be done with $data
     * @param mixed $data
     * @return void
     */
    public function handle( $data )
    {
        $content = $this->getContent();
        $body = $this->templating->render(
            $this->template,
            array(
                'data' => $data,
                'content' => $content,
            )
        );
        $message = \Swift_Message::newInstance();
        $message->setSubject("Sent Data");
        $message->setTo( $content->getFieldValue( 'recipient' )->text );
        $message->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}
```

#### SingleHandlerProvider

For simple use cases where you use the same handler for all of your forms, a SingleHandlerProvider is available:

```yml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_handler_provider:
    class: %heliopsis_ezforms.handler_provider.single_handler.class%
    arguments: [@acme_forms.handlers.logger, @ezpublish.api.service.content]

```


### Views

Views are ordinary content views. They are rendered by eZPublish's ViewManager so you may rely on your ordinary
view providers matching configuration. You only need to use the bundle's controller service for your main views.
Your template will be passed the symfony form view in a `form` parameter.

```yml
# ezpublish/config/ezpublish.yml

ezpublish:
  system:
    frontend_group:
      location_view:
        full:
          form:
            controller: heliopsis_ezforms.controller:formAction
            template: AcmeFormsBundle:full:form.html.twig
            match:
              Identifier\ContentType: 'form'
```

```twig

{# Acme/FormsBundle/Resources/views/full/form.html.twig #}

{% extends 'AcmeFormsBundle::pagelayout.html.twig' %}

{% block content %}
    <section>
        <header>
            <h1>{{ ez_content_name( content ) }}</h1>
            {{ ez_render_field( content, 'introduction_text' ) }}
        </header>

    {{ form( form ) }}

    </section>
{% endblock content %}

```


The `confirm` view used by default ResponseProvider is nothing but a dedicated view for your eZPublish location,
it's up to you to configure what should be displayed on this page.
Note that the form controller should not be used at this staged unless you want to display another form.


```yml
# ezpublish/config/ezpublish.yml

ezpublish:
  system:
    frontend_group:
      location_view:
        confirm:
          form:
            template: AcmeFormsBundle:full:form_confirm.html.twig
            match:
              Identifier\ContentType: 'form'
```

```twig

{# Acme/FormsBundle/Resources/views/full/form_confirm.html.twig #}

{% extends 'AcmeFormsBundle::pagelayout.html.twig' %}

{% block content %}
    <section>
        <header>
            <h1>{{ ez_content_name( content ) }}</h1>
        </header>

        {{ ez_render_field( content, 'confirmation_text' ) }}

    </section>
{% endblock content %}

```


Advanced Usage
--------------

### Response provider

If you want to go beyond simple one-off forms and say chain multiple forms to create some kind of funnel,
you can define a custom ResponseProvider the way you already defined custom form and handler providers :


```php
<?php
//Acme/FormsBundle/Providers/MultiStepResponseProvider.php

namespace Acme\FormsBundle\Providers;

use Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface;
use Heliopsis\eZFormsBundle\Exceptions\BadConfigurationException;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Acme\FormsBundle\MultiStepFormData;

class MultiStepResponseProvider implements ResponseProviderInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    function __construct(UrlGeneratorInterface $urlGenerator )
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(Location $location, $data)
    {
        if( !$data instanceof MultiStepFormData )
        {
            throw new BadConfigurationException( "Response Provider can only be used with MultiStepFormData" );
        }

        $nextStep = $data->getNextStep();

        return new RedirectResponse(
            $this->urlGenerator->generate(
                $nextStep->getRoute(),
                $nextStep->getRouteParams()
            );
        );
    }
}
```

You then need to define your provider as a service:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

parameters:
    acme_forms.multistep_response_provider.class: Acme\FormsBundle\Providers\MultiStepResponseProvider

services:
  acme_forms.multistep_response_provider:
    class: %acme_forms.multistep_response_provider.class%
    arguments: [@router]

```

... and tell ezforms-bundle to use it as your default response provider:

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  providers:
    response: acme_forms.multistep_response_provider
```


### Custom facade

Finally, maybe you've got yourself in a much more complex situation where you need to closely couple form generation,
data handling and / or funnel logic. This can be achieved with a custom facade :


```php
<?php
//Acme/FormsBundle/FormFacade/CustomFormFacade.php

namespace Acme\FormsBundle\FormFacade;

use Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class CustomFormFacade implements FormFacadeInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException
     */
    public function getForm( Location $location )
    {
        ...
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        ...
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(Location $location, $data)
    {
        ...
    }

}

```


```yaml
# Acme/FormsBundle/Resources/config/services.yml

parameters:
    acme_forms.custom_facade.class: Acme\FormsBundle\Providers\MultiStepResponseProvider

services:
  acme_forms.custom_facade:
    class: %acme_forms.custom_facade.class%
    ...

```

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  facade: acme_forms.custom_facade
```
