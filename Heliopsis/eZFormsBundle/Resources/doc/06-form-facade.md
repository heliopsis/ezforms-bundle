# Form facade

The `FormFacadeInterface` aggregates the 3 providers interfaces (`FormProviderInterface`, `HandlerProviderInterface`
and `ResponseProviderInterface`). Default implementation merely aggregates 3 independant services, but it is through the
facade that the controller interacts with those services.

What that means is that if you've got yourself in a situation where you need to closely couple form generation,
data handling and / or funnel logic, this can be achieved with a custom facade.

Here is a naive and incomplete funneling facade:

```php
<?php
//Acme/FormsBundle/FormFacade/CustomFunnelFormFacade.php

namespace Acme\FormsBundle\FormFacade;

use Heliopsis\eZFormsBundle\FormFacade\FormFacadeInterface;
use Heliopsis\eZFormsBundle\Exceptions\UnknownFormException;
use Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\Location;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class CustomFunnelFormFacade implements FormFacadeInterface
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Symfony\Component\Form\FormInterface
     * @throws \Heliopsis\eZFormsBundle\Exceptions\UnknownFormException
     */
    public function getForm( Location $location )
    {
        switch ( $this->getCurrentStep() )
        {
            case self::STEP1:
                // instantiate and return form for step1
                break;

            case self::STEP2:
                // instantiate and return form for step2
                break;
        }
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @return \Heliopsis\eZFormsBundle\FormHandler\FormHandlerInterface
     */
    public function getHandler( Location $location )
    {
        if ( $this->getCurrentStep() == $this->getLastStep() )
        {
            return $this->getFinalHandler();
        }

        return $this->getIntermediateHandler();
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param mixed $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(Location $location, $data)
    {
        if ( $this->getCurrentStep() == $this->getLastStep() )
        {
            return $this->getConfirmRedirectResponse( $location, $data );
        }

        return $this->getRedirectToSelfResponse( $location, $data );
    }

}

```


```yaml
# Acme/FormsBundle/Resources/config/services.yml

parameters:
    acme_forms.custom_funnel_facade.class: Acme\FormsBundle\FormFacade\CustomFunnelFormFacade

services:
  acme_forms.custom_funnel_facade:
    class: %acme_forms.custom_funnel_facade.class%
    ...

```

```yaml
# ezpublish/config/config.yml

heliopsis_ezforms:
  facade: acme_forms.custom_funnel_facade
```
