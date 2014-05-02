ezforms-bundle
==============

This bundle provides a flexible way to associate [Symfony forms](http://symfony.com/doc/current/book/forms.html) to [eZPublish](https://github.com/ezsystems/ezpublish-community) contents.

Features:

- Form controller extending eZPublish's view controller
- Facade pattern for flexible form handling
- Separate interfaces for form instanciation, data handling and response generation
- Abstract classes for content related data handling
- Unit tests

*NB:* this bundle does not provide out of the box forms in eZPublish, it rather gives you tools to easily define
custom forms and leverage eZPublish's content tree to access or configure those forms.


License
-------

This bundle is released under GPL2


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

### 2. Enable bundle in `EzPublishKernel.php`

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

*NB: see Usage section for service definitions*


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

Documentation
-------------

Detailed documentation is available in the [Resources/doc folder](/Heliopsis/eZFormsBundle/Resources/doc)
