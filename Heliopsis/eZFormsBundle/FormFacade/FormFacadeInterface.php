<?php
/**
 * @author: bchoquet
 */

namespace Heliopsis\eZFormsBundle\FormFacade;

use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;
use Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface;

interface FormFacadeInterface extends FormProviderInterface, HandlerProviderInterface, ResponseProviderInterface
{
}