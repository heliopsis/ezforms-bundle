# Bundle semantic Configuration

The bundle lets you configure which facade should be injected in the controller.
When using the default facade, you may (and should) configure which providers to use.

```yml

Default configuration for extension with alias: "heliopsis_ezforms"
heliopsis_ezforms:

    # FormFacadeInterface service ID to use in controller
    facade:               heliopsis_ezforms.facade.default

    # These providers are automatically injected into default facade
    providers:

        # FormProvider service to use as heliopsis_ezforms.form_provider
        form:                 ~

        # HandlerProvider service to use as heliopsis_ezforms.handler_provider
        handler:              heliopsis_ezforms.handler_provider.null

        # ResponseProvider service to use as heliopsis_ezforms.response_provider
        response:             heliopsis_ezforms.response_provider.redirect_confirm
```