# Form providers

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


## Available Providers

The bundle includes a few utility providers for basic usage :

- [ContentRemoteIdMap](#ContentRemoteIdMap)
- [ContentTypeIdentifierMap](#ContentTypeIdentifierMap)
- [Chain](#Chain)

### ContentRemoteIdMap

Maps _Content_ remote ids to form types.

Useful for small websites with only a handful of custom forms.

Content remote ids can be read from the administration interface (in the _details_ tab
of the content view) or you can explicitly set them when creating content using the public API.

Define a new service using the `ContentRemoteIdMap` class and add a call to the `addFormType` method for each of your contents:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_map_form_provider:
    class: %heliopsis_ezforms.form_provider.content_remoteid_map.class%
    arguments: [ @form.factory ]
    calls:
      - [ addFormType, [ 'FEEDBACK_FORM', 'acme_forms_feedback' ] ]
      - [ addFormType, [ 'NEWSLETTER_REGISTRATION', @acme_forms.newsletter_registration.type ] ]

```

When called, the `getForm()` method will use Symfony's form factory to create a new form of the specified type.
Types passed to the `addFormType()` method can be either the form type alias string or a service implementing `Symfony\Component\Form\FormTypeInterface`

### ContentTypeIdentifierMap

Maps _ContentType_ identifiers to form types.

Define a new service using the `ContentTypeIdentifierMap` class and add a call to the `addFormType` method for each of your content types:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  acme_forms.custom_map_form_provider:
    class: %heliopsis_ezforms.form_provider.content_type_identifier_map.class%
    arguments: [ @form.factory ]
    calls:
      - [ addFormType, [ 'article', 'acme_forms_feedback' ] ]
      - [ addFormType, [ 'form', @acme_forms.newsletter_registration.type ] ]

```

When called, the `getForm()` method will use Symfony's form factory to create a new form of the specified type.
Types passed to the `addFormType()` method can be either the form type alias string or a service implementing `Symfony\Component\Form\FormTypeInterface`

### Chain

Combines multiple form providers with priority options. The first form to be actually returned by one of the children providers will be used.

Use it if you have mutiple rules involving different kinds of matching (i.e. Try to match against a specific
_Content_ remote id and fallback to your regular `ContentTypeIdentifierMap` provider.

Define a new service using the `Chain` class and add a call to the `addProvider` method for each of yours providers
with an optional `priority` parameter:

```yaml
# Acme/FormsBundle/Resources/config/services.yml

services:
  # ContentTypeIdentifierMap from previous example
  acme_forms.content_type_identifier_map_form_provider:
    class: %heliopsis_ezforms.form_provider.content_type_identifier_map.class%
    arguments: [ @form.factory ]
    calls:
      - [ addFormType, [ 'article', 'acme_forms_feedback' ] ]
      - [ addFormType, [ 'form', @acme_forms.newsletter_registration.type ] ]
      
  # ContentRemoteIdMap from previous example
  acme_forms.content_remote_id_map_form_provider:
    class: %heliopsis_ezforms.form_provider.content_remoteid_map.class%
    arguments: [ @form.factory ]
    calls:
      - [ addFormType, [ 'FEEDBACK_FORM', 'acme_forms_feedback' ] ]
      - [ addFormType, [ 'NEWSLETTER_REGISTRATION', @acme_forms.newsletter_registration.type ] ]

  # Combine those two providers giving higher priority to the ContentRemoteIdMap
  acme_forms.custom_map_form_provider:
    class: %heliopsis_ezforms.form_provider.chain.class%
    calls:
        # Default priority is 0
      - [ addProvider, [ @acme_forms.content_type_identifier_map ] ]
      - [ addProvider, [ @acme_forms.content_remote_id_map_form_provider, 3 ] ]

```

Services passed to the `addProvider` call must implement `Heliopsis\eZFormsBundle\Provider\FormProviderInterface`.