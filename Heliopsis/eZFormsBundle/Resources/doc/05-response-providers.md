# Response providers

Response providers are in charge of generating an HttpResponse object when the form has successfully been submitted and handled.
Default behaviour is to redirect to a confirm view of current location.

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

