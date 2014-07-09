# Views

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
