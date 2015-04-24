# Handler providers

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
     * @param FormHandlerInterface $formHandler
     */
    function __construct(FormHandlerInterface $formHandler)
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

## Available Providers

The bundle includes a few utility providers for basic usage :

- [SingleHandlerProvider](#SingleHandlerProvider)
- [ContentRemoteIdMap](#ContentRemoteIdMap)
- [ContentTypeIdentifierMap](#ContentTypeIdentifierMap)
- [Chain](#Chain)

### SingleHandlerProvider

For simple use cases where you use the same handler for all of your forms, a SingleHandlerProvider is available:

```yml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_handler_provider:
    class: %heliopsis_ezforms.handler_provider.single_handler.class%
    arguments: [@acme_forms.handlers.logger]

```

### ContentRemoteIdMap

Maps _Content_ remote ids to form handlers, similar to the [eponymous form provider](01-form-providers.md#contentremoteidmap).

Define a new service using the `ContentRemoteIdMap` class and add a call to the `addFormType` method for each of your contents:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_map_handler_provider:
    class: %heliopsis_ezforms.handler_provider.content_remoteid_map.class%
    arguments: [ @ezpublish.api.service.content ]
    calls:
      - [ addFormHandler, [ 'FEEDBACK_FORM', @acme_forms.handlers.admin_email ] ]
      - [ addFormHandler, [ 'NEWSLETTER_REGISTRATION', @acme_forms.handlers.mailchimp_subscription ] ]
```

Services passed to the `addFormHandler()` method must implement `Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface`.

### ContentTypeIdentifierMap

Maps _ContentType_ identifiers to form types, similar to the [eponymous form provider](01-form-providers.md#contenttypeindentifiermap).

Define a new service using the `ContentTypeIdentifierMap` class and add a call to the `addFormType` method for each of your contents:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_map_handler_provider:
    class: %heliopsis_ezforms.handler_provider.content_type_identifier_map.class%
    arguments: [ @ezpublish.api.service.content_type ]
    calls:
      - [ addFormHandler, [ 'article', @acme_forms.handlers.admin_email ] ]
      - [ addFormHandler, [ 'comments', @acme_forms.handlers.mailchimp_comments ] ]
```

Services passed to the `addFormHandler()` method must implement `Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface`.

### Chain

Combines multiple handler providers with priority options. The first handler to be actually returned by one of the children providers will be used.
Similar to the [eponymous form provider](01-form-providers.md#chain)

Use it if you have mutiple rules involving different kinds of matching (i.e. Try to match against a specific
_Content_ remote id and fallback to your regular `SingleHandlerProvider`.

Define a new service using the `Chain` class and add a call to the `addProvider` method for each of your contents
with an optional `priority` parameter:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  # ContentRemoteIdMap from previous example
  acme_forms.content_remote_id_handler_provider:
    class: %heliopsis_ezforms.handler_provider.content_remoteid_map.class%
    arguments: [ @ezpublish.api.service.content ]
    calls:
      - [ addFormHandler, [ 'FEEDBACK_FORM', @acme_forms.handlers.admin_email ] ]
      - [ addFormHandler, [ 'NEWSLETTER_REGISTRATION', @acme_forms.handlers.mailchimp_subscription ] ]

  # Default provider to at least log data
  acme_forms.default_handler_provider:
    class: %heliopsis_ezforms.handler_provider.single_handler.class%
    arguments: [@acme_forms.handlers.logger]

  # Combine those two providers giving higher priority to the ContentRemoteIdMap
  acme_forms.custom_map_handler_provider:
    class: %heliopsis_ezforms.handler_provider.chain.class%
    calls:
        # Default priority is 0
      - [ addProvider, [ @acme_forms.default_handler_provider ] ]
      - [ addProvider, [ @acme_forms.content_remote_id_handler_provider, 4 ] ]
```

Services passed to the `addProvider()` method must implement `Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface`.