<?php
/**
 * @copyright: Copyright (C) 2014 Heliopsis. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 */

namespace Heliopsis\eZFormsBundle\FormFacade;

use Heliopsis\eZFormsBundle\Provider\FormProviderInterface;
use Heliopsis\eZFormsBundle\Provider\HandlerProviderInterface;
use Heliopsis\eZFormsBundle\Provider\ResponseProviderInterface;

interface FormFacadeInterface extends FormProviderInterface, HandlerProviderInterface, ResponseProviderInterface
{
}
